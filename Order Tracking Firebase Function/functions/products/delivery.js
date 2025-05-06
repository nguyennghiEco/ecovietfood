const functions = require('firebase-functions');
const admin = require ("firebase-admin");
const firestore = admin.firestore();

/*
** Dispatch orders to vendors and drivers
*/
exports.dispatch = functions.firestore
.document("restaurant_orders/{orderID}")
.onWrite(async (change, context) => {

    const orderData = change.after.data();
    const beforeData = change.before.data();
    if (beforeData && orderData) {
        const keysChanged = Object.keys(orderData).filter(
            key => JSON.stringify(orderData[key]) !== JSON.stringify(beforeData[key])
        );

        // If only orderAutoCancelAt changed, return early
        if (keysChanged.length === 1 && keysChanged.includes('orderAutoCancelAt')) {
            console.log("orderAutoCancelAt update detected, skipping dispatch logic.");
            return null;
        }
    }
    if (!orderData) {
        console.log("No order data");
        return;
    }

    if (orderData.status === "Order Cancelled") {
        console.log("Order #" + change.after.ref.id + " was cancelled.")
        return null
    }

    if (orderData.status === "Order Placed") {
        // this is a new order, so we need to send it to the vendor for approval
        console.log("Order #" + change.after.ref.id + " was sent to vendor for approval.")
        return null
    }

    if (orderData.takeAway === true) {
        // this is a new order, so we need to send it to the vendor for approval
        console.log("Order #" + change.after.ref.id + " was sent as takeAway to vendor for approval.")
        return null
    }

   if (orderData.status === "Order Accepted" || orderData.status === "Driver Rejected") {
        // the vendor accepted the order, so we need to find an available driver
        console.log("Finding a driver for order #" + change.after.ref.id)

        const rejectedByDrivers = orderData.rejectedByDrivers ? orderData.rejectedByDrivers : []

        var orderId = change.after.ref.id;
        var driverNearByData = await getDriverNearByData();
        var minimumDepositToRideAccept = 0;
        var orderAcceptRejectDuration = 0;
        var orderAutoCancelDuration = 0;
        var kDistanceRadiusForDispatch = 50;
        var singleOrderReceive = false;
       
        
        var zone_id = null;
        if(orderData.address.location.longitude && orderData.address.location.latitude){
            zone_id = await getUserZoneId(orderData.address.location.longitude,orderData.address.location.latitude);
            console.log('Zone id by address',zone_id);
        }
        
        if(driverNearByData !== undefined){
            if(driverNearByData.minimumDepositToRideAccept !== undefined){
                minimumDepositToRideAccept = parseInt(driverNearByData.minimumDepositToRideAccept);
            }
            if(driverNearByData.driverOrderAcceptRejectDuration !== undefined){
                 orderAcceptRejectDuration = parseInt(driverNearByData.driverOrderAcceptRejectDuration);
            }
            if(driverNearByData.orderAutoCancelDuration !== undefined){
                 orderAutoCancelDuration = parseInt(driverNearByData.orderAutoCancelDuration);
            }
            if(driverNearByData.driverRadios !== undefined){
                 kDistanceRadiusForDispatch = parseInt(driverNearByData.driverRadios);
            }
            if(driverNearByData.distanceType !== undefined){
               
                if(driverNearByData.distanceType=='miles'){
                    kDistanceRadiusForDispatch = parseInt(driverNearByData.driverRadios * 1.60934);
                }
                 
            }

            if(driverNearByData.singleOrderReceive !== undefined){
                 singleOrderReceive = driverNearByData.singleOrderReceive;
            }
        }

        console.log('minimumDepositToRideAccept',minimumDepositToRideAccept);
        console.log('orderAcceptRejectDuration',orderAcceptRejectDuration);

        // change.after.ref.set({ status: "Pending Driver" }, {merge: true})
        return firestore
            .collection("users")
            .where('role', '==', "driver")
            .where('isActive', '==', true)
            .where('wallet_amount', '>=', minimumDepositToRideAccept)
            .get()
            .then(snapshot => {
                var found = false
                snapshot.forEach(doc => {
                    if (!found) {
                        // We simply assign the first available driver who's within a reasonable distance from the vendor and who did not reject the order and who is not delivering already
                        const driver = doc.data();
                        if(driver.hasOwnProperty('fcmToken') && driver.fcmToken!=null && driver.fcmToken!=''){
                            console.log(driver)

                            if (driver.hasOwnProperty('zoneId') && zone_id !== null && driver.zoneId !== zone_id) {
                                // We did not find an available driver in the zone
                                console.log("Could not find an available driver in the zone #"+zone_id+ "for order #" + change.after.ref.id)
                                return null
                            }

                            if (driver.location && rejectedByDrivers.indexOf(driver.id) === -1) {
                                const vendor = orderData.vendor
                                if (vendor) {
                                    const distance = distanceRadius(driver.location.latitude, driver.location.longitude, vendor.latitude, vendor.longitude)
                                    console.log("Driver (" + driver.email + " Location: ")
                                    console.log(driver.location)
                                    console.log("Vendor Location: lat " + vendor.latitude + " long" + vendor.longitude)
                                    console.log(distance)
                                    if (distance < kDistanceRadiusForDispatch) {
                                        found = true

                                        //set data for notification
                                        var time = Math.floor(orderAcceptRejectDuration / 60) + ":" + (orderAcceptRejectDuration % 60 ? orderAcceptRejectDuration % 60 : '00');
                                        var message = {
                                            notification:{
                                            title: 'New order received',
                                            body: 'You have a new order, please accept the order in '+time+' mins'
                                            },
                                            token: driver.fcmToken
                                        };
                                        //send notification to driver
                                        admin.messaging().send(message).then((response) => {
                                            console.log('Notification Success:',response);
                                            return null
                                        }).catch((error) => {
                                            console.log('Notification Error:',error);
                                        });

                                        // We update the order status
                                        change.after.ref.set({ status: "Driver Pending" }, {merge: true})
                                        .then(async function (result) {
                                            // After update the order status get new updated status
                                            firestore.collection("restaurant_orders").doc(orderId).get().then((querySnapshot) => {	
                                                var newOrderData = querySnapshot.data();
                                                // Check if driver is accepting the order within defined time or not
                                                if(orderAcceptRejectDuration > 0 && newOrderData.status === "Driver Pending"){
                                                    setTimeout(function(){ 
                                                        // Re-check order status after time limit exceed before find out other driver
                                                        firestore.collection("restaurant_orders").doc(orderId).get().then((querySnapshot) => {
                                                            var newOrderData2 = querySnapshot.data();
                                                            // If order status is driver pending then and only we will find new driver and current driver will add to rejected list
                                                            if(newOrderData2.status === "Driver Pending"){
                                                                // We changed the ordering method to assign multiple orders to single driver so now find orderRequestData for current driver
                                                                firestore.collection("users").doc(driver.id).get().then((querySnapshot) => {
                                                                    var driverData = querySnapshot.data();
                                                                    //Now remove current orderId from all assigned order to this driver because they have not accepted current order in time
                                                                    if(driverData.orderRequestData !== undefined){
                                                                        const newOrderRequestData = driverData.orderRequestData.filter(function (oid) {
                                                                            return oid !== orderId;
                                                                        });
                                                                        //Now update new orderRequestData after removing current order
                                                                        firestore.collection('users').doc(driver.id).update({
                                                                            'orderRequestData': newOrderRequestData,
                                                                        });
                                                                    }
                                                                    // Current driver is adding to rejected list so they will not receive order again and update status to find new driver
                                                                    rejectedByDrivers.push(driver.id);
                                                                    firestore.collection('restaurant_orders').doc(orderId).update({
                                                                        'status': 'Order Accepted',
                                                                        'rejectedByDrivers': rejectedByDrivers
                                                                    })
                                                                    console.log("Order not accepted by driver #" + driver.id + " for order #" + orderId + " within " + orderAcceptRejectDuration + " seconds, searching for next driver.")
                                                                    return null
                                                                }).catch(error => {
                                                                    console.log(error)
                                                                })
                                                            }
                                                            return null
                                                        })
                                                        .catch(error => {
                                                            console.log(error)
                                                        })
                                                    },orderAcceptRejectDuration*1000);
                                                }
                                                return null
                                            })
                                            .catch(error => {
                                                console.log(error)
                                            })
                                            return null
                                        })
                                        .catch(error => {
                                            console.log(error)
                                        })
                                        // We changed the ordering method to assign multiple orders to single driver
                                        // We send the order to the driver, by appending orderRequestData to the driver's user model in the users table
                                        var orderRequestData = [];
                                        if(driver.orderRequestData !== undefined){
                                            if(singleOrderReceive === false){
                                                if(driver.orderRequestData.length === 0){
                                                    orderRequestData.push(orderId);
                                                }else{
                                                    if(driver.orderRequestData.indexOf(orderId) === -1) {
                                                        driver.orderRequestData.push(orderId);
                                                    }
                                                    orderRequestData = driver.orderRequestData;
                                                }
                                            }else{
                                                orderRequestData.push(orderId);
                                            }
                                        }else{
                                            orderRequestData.push(orderId);
                                        }
                                        firestore.collection('users').doc(driver.id).update({
                                            orderRequestData: orderRequestData,
                                        });

                                        console.log("Order sent to driver #" + driver.id + " for order #" + change.after.ref.id + " with distance at " + distance)
                                    }
                                }
                            }
                        }
                    }
                })
                if (!found) {
                    // We did not find an available driver
                    const currentTime = new Date();
                    const futureTime = new Date(currentTime.getTime() + orderAutoCancelDuration * 60 * 1000);
                    const firebaseTimestamp = admin.firestore.Timestamp.fromDate(futureTime);
                    firestore.collection('restaurant_orders').doc(orderId).update({orderAutoCancelAt: firebaseTimestamp})
                    console.log("Could not find an available driver for order #" + change.after.ref.id)
                }
                return null
            })
            .catch(error => {
                console.log(error)
            })
    }

    if (orderData.status === "Driver Accepted") {
        // Vendor accepted, driver accepted, so we update the delivery status
        change.after.ref.set({ status: "Order Shipped" }, {merge: true})
        console.log("Order #" + change.after.ref.id + " was shipped")
        return null
    }
    return null
});

const distanceRadius = (lat1, lon1, lat2, lon2) => {
	if ((lat1 === lat2) && (lon1 === lon2)) {
		return 0;
	}
	else {
		var radlat1 = Math.PI * lat1/180;
		var radlat2 = Math.PI * lat2/180;
		var theta = lon1-lon2;
		var radtheta = Math.PI * theta/180;
		var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
		if (dist > 1) {
			dist = 1;
		}
		dist = Math.acos(dist);
		dist = dist * 180/Math.PI;
		dist = dist * 60 * 1.1515;
        dist = dist * 1.60934; // Convert to kilometers
        
		return dist;
	}
}

async function getDriverNearByData(){
    var snapshot =  await firestore.collection("settings").doc('DriverNearBy').get();
    return snapshot.data();
}

async function getUserZoneId(address_lng,address_lat){
    var zone_id = null;
    var zone_list = [];
    var snapshots = await firestore.collection('zone').where("publish","==",true).get();
    if(snapshots.docs.length > 0){
        snapshots.docs.forEach((snapshot) => {
            var zone_data = snapshot.data();
            zone_list.push(zone_data);
        });   
    }
    if(zone_list.length > 0){
        for (i = 0; i < zone_list.length; i++) {
            var zone = zone_list[i];
            var vertices_x = [];
            var vertices_y = [];
            for (j = 0; j < zone.area.length; j++) {
                var geopoint = zone.area[j];
                vertices_x.push(geopoint.longitude);
                vertices_y.push(geopoint.latitude);
            }
            var points_polygon = (vertices_x.length)-1; 
            if(is_in_polygon(points_polygon, vertices_x, vertices_y, address_lng, address_lat)){
                zone_id = zone.id;
                console.log("Matched zone id by address",zone_id);
                break; 
            }
        }
    }
    return zone_id;
}

function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y){
    $i = $j = $c = $point = 0;
    for ($i = 0, $j = $points_polygon ; $i < $points_polygon; $j = $i++) {
        $point = $i;
        if( $point === $points_polygon )
            $point = 0;
        if ( (($vertices_y[$point]  >  $latitude_y !== ($vertices_y[$j] > $latitude_y)) && ($longitude_x < ($vertices_x[$j] - $vertices_x[$point]) * ($latitude_y - $vertices_y[$point]) / ($vertices_y[$j] - $vertices_y[$point]) + $vertices_x[$point]) ) )
            $c = !$c;
    }
    return $c;
}