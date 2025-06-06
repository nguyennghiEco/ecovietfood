import 'dart:async';
import 'dart:developer';

import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:restaurant/constant/send_notification.dart';
import 'package:restaurant/models/conversation_model.dart';
import 'package:restaurant/models/inbox_model.dart';
import 'package:restaurant/utils/fire_store_utils.dart';
import 'package:uuid/uuid.dart';

class ChatController extends GetxController {
  Rx<TextEditingController> messageController = TextEditingController().obs;

  final ScrollController scrollController = ScrollController();

  @override
  void onInit() {
    // TODO: implement onInit
    if (scrollController.hasClients) {
      Timer(
          const Duration(milliseconds: 500),
          () => scrollController
              .jumpTo(scrollController.position.maxScrollExtent));
    }
    getArgument();
    super.onInit();
  }

  RxBool isLoading = true.obs;
  RxString orderId = "".obs;
  RxString customerId = "".obs;
  RxString customerName = "".obs;
  RxString customerProfileImage = "".obs;
  RxString restaurantId = "".obs;
  RxString restaurantName = "".obs;
  RxString restaurantProfileImage = "".obs;
  RxString token = "".obs;
  RxString chatType = "".obs;

  getArgument() {
    dynamic argumentData = Get.arguments;
    if (argumentData != null) {
      orderId.value = argumentData['orderId'];
      customerId.value = argumentData['customerId'];
      customerName.value = argumentData['customerName'];
      customerProfileImage.value = argumentData['customerProfileImage'] ?? "";
      restaurantId.value = argumentData['restaurantId'];
      restaurantName.value = argumentData['restaurantName'];
      restaurantProfileImage.value =
          argumentData['restaurantProfileImage'] ?? "";
      token.value = argumentData['token'];
      chatType.value = argumentData['chatType'];
    }
    isLoading.value = false;
  }

  sendMessage(String message, Url? url, String videoThumbnail,
      String messageType) async {
    InboxModel inboxModel = InboxModel(
        lastSenderId: restaurantId.value,
        customerId: customerId.value,
        customerName: customerName.value,
        restaurantId: restaurantId.value,
        restaurantName: restaurantName.value,
        createdAt: Timestamp.now(),
        orderId: orderId.value,
        customerProfileImage: customerProfileImage.value,
        restaurantProfileImage: restaurantProfileImage.value,
        lastMessage: messageController.value.text,
        chatType: chatType.value);
    if (chatType.value == 'customer') {
      await FireStoreUtils.addRestaurantInbox(inboxModel);
    }
    if (chatType.value == 'admin') {
      await FireStoreUtils.addAdminInbox(inboxModel);
    }

    ConversationModel conversationModel = ConversationModel(
        id: const Uuid().v4(),
        message: message,
        senderId: restaurantId.value,
        receiverId: customerId.value,
        createdAt: Timestamp.now(),
        url: url,
        orderId: orderId.value,
        messageType: messageType,
        videoThumbnail: videoThumbnail);
    if (url != null) {
      if (url.mime.contains('image')) {
        conversationModel.message = "sent a message";
      } else if (url.mime.contains('video')) {
        conversationModel.message = "Sent a video";
      } else if (url.mime.contains('audio')) {
        conversationModel.message = "Sent a audio";
      }
    }
    log("messageType :: ${chatType.value}");
    if (chatType.value == 'customer') {
      await FireStoreUtils.addRestaurantChat(conversationModel);
      if (token.value != '' && token.value.isEmpty) {
        SendNotification.sendChatFcmMessage(customerName.value,
            conversationModel.message.toString(), token.value, {});
      }
    }
    if (chatType.value == 'admin') {
      await FireStoreUtils.addAdminChat(conversationModel);
      if (token.value != '' && token.value.isEmpty) {
        SendNotification.sendChatFcmMessage(customerName.value,
            conversationModel.message.toString(), token.value, {});
      }
    }
  }

  final ImagePicker imagePicker = ImagePicker();
}
