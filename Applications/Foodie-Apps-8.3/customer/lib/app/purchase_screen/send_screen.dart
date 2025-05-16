import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:get/get_core/src/get_main.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

import '../../constant/constant.dart';
import '../../themes/app_them_data.dart';
import '../../themes/round_button_fill.dart';
import '../../utils/dark_theme_provider.dart';

class SendScreen extends StatelessWidget {
  final String transId;

  const SendScreen({super.key, required this.transId});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    return Scaffold(
      appBar: AppBar(
        backgroundColor: themeChange.getThem() ? AppThemeData.surfaceDark : AppThemeData.surface,
        centerTitle: false,
        titleSpacing: 0,
        title: Text(
          "Nạp tiền",
          textAlign: TextAlign.start,
          style: TextStyle(
            fontFamily: AppThemeData.medium,
            fontSize: 16,
            color: themeChange.getThem() ? AppThemeData.grey50 : AppThemeData.grey900,
          ),
        ),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            Text("Hãy chuyển khoản và paste mã giao dịch bên dưới vào phân ghi chú trong ứng dụng ngân hàng"),
            const SizedBox(
              height: 8,
            ),
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: themeChange.getThem() ? AppThemeData.grey900 : AppThemeData.grey50,
                borderRadius: BorderRadius.circular(8),
              ),
              child: Row(
                children: [
                  Expanded(
                    child: Text(
                      transId,
                      style: TextStyle(
                        fontFamily: AppThemeData.semiBold,
                        color: themeChange.getThem() ? AppThemeData.grey50 : AppThemeData.grey900,
                      ),
                    ),
                  ),
                  IconButton(
                      onPressed: () {
                        Clipboard.setData(ClipboardData(text: transId));
                        Get.snackbar("Success", "Mã giao dịch đã được sao chép vào clipboard");
                      },
                      icon: const Icon(Icons.copy)),
                ],
              ),
            ),
            Image.asset(
              "assets/images/qr.jpg",
              width: MediaQuery.of(context).size.width,
            )
          ],
        ),
      ),
      bottomNavigationBar: Container(
        color: themeChange.getThem() ? AppThemeData.grey900 : AppThemeData.grey50,
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
        child: Padding(
          padding: const EdgeInsets.only(bottom: 20),
          child: RoundedButtonFill(
            title: "Hoàn tất giao dịch",
            height: 5.5,
            color: AppThemeData.primary300,
            textColor: AppThemeData.grey50,
            fontSizes: 16,
            onPress: () {
              Navigator.pop(context);
              Get.snackbar("Success", "Giao dịch đã được ghi nhận");
            },
          ),
        ),
      ),
    );
  }
}
