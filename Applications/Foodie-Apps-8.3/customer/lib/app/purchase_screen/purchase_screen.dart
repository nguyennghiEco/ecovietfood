import 'package:customer/app/purchase_screen/send_screen.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

import '../../constant/constant.dart';
import '../../themes/app_them_data.dart';
import '../../themes/round_button_fill.dart';
import '../../themes/text_field_widget.dart';
import '../../utils/dark_theme_provider.dart';

class PurchaseScreen extends StatefulWidget {
  const PurchaseScreen({super.key});

  @override
  State<PurchaseScreen> createState() => _PurchaseScreenState();
}

class _PurchaseScreenState extends State<PurchaseScreen> {
  late final TextEditingController _amountController = TextEditingController();
  late final TextEditingController _messageController = TextEditingController();
  late final TextEditingController _previewController = TextEditingController();

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
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              TextField(
                controller: _previewController,
                textAlign: TextAlign.center,
                style: TextStyle(
                    color: themeChange.getThem() ? AppThemeData.grey50 : AppThemeData.grey900,
                    fontFamily: AppThemeData.black,
                    fontSize: 40),
                decoration: InputDecoration(
                  labelStyle: TextStyle(
                    color: themeChange.getThem() ? AppThemeData.grey50 : AppThemeData.grey900,
                  ),
                  hintText: '0 đ',
                  border: InputBorder.none,
                ),
              ),
              TextFieldWidget(
                title: 'Choose an amount'.tr,
                controller: _amountController,
                hintText: 'Nhập số tiền tại đây....',
                textInputType: const TextInputType.numberWithOptions(signed: true, decimal: true),
                textInputAction: TextInputAction.done,
                inputFormatters: [
                  FilteringTextInputFormatter.allow(RegExp(r'[0-9.,]')),
                ],
                prefix: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                  child: Text(
                    "${Constant.currencyModel!.symbol}".tr,
                    style: TextStyle(
                        color: themeChange.getThem() ? AppThemeData.grey50 : AppThemeData.grey900,
                        fontFamily: AppThemeData.semiBold,
                        fontSize: 18),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
      bottomNavigationBar: Container(
        color: themeChange.getThem() ? AppThemeData.grey900 : AppThemeData.grey50,
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
        child: Padding(
          padding: const EdgeInsets.only(bottom: 20),
          child: RoundedButtonFill(
            title: "Xác nhận",
            height: 5.5,
            color: AppThemeData.primary300,
            textColor: AppThemeData.grey50,
            fontSizes: 16,
            onPress: () async {
              if (_amountController.text.isEmpty) {
                Get.snackbar("Error", "Vui lòng nhập số tiền");
                return;
              }
              final dateFormatter = DateFormat('yyMMddHHmm');
              final transactionId = '${Constant.userModel?.email?.split('@').first}-${dateFormatter.format(DateTime.now())}-${_amountController.text.padLeft(4, '0')}';
              await Get.to(SendScreen(
                transId: transactionId,
              ));
              if (context.mounted) {
                Navigator.pop(context);
              }
            },
          ),
        ),
      ),
    );
  }

  final NumberFormat currencyFormatter = NumberFormat("#,###", "vi_VN");

  @override
  void initState() {
    super.initState();
    _amountController.addListener(() {
      final input = double.tryParse(_amountController.text) ?? 0;
      final formatted = currencyFormatter.format((input * 1000).toInt());
      _previewController.text = "$formatted đ";
    });
  }

  @override
  void dispose() {
    _amountController.dispose();
    _messageController.dispose();
    _previewController.dispose();
    super.dispose();
  }
}
