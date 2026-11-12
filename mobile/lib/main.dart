import 'dart:io';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'core/audio/audio_service.dart';
import 'core/constants/app_colors.dart';
import 'services/api_service.dart';
import 'services/game_engine_service.dart';
import 'screens/home_screen.dart';

class BibliaShowHttpOverrides extends HttpOverrides {
  @override
  HttpClient createHttpClient(SecurityContext? context) {
    return super.createHttpClient(context)
      ..badCertificateCallback = (X509Certificate cert, String host, int port) => true;
  }
}

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  HttpOverrides.global = BibliaShowHttpOverrides();
  await AudioService.instance.init();
  await ApiService.instance.init();

  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => GameEngineService()),
      ],
      child: const BibliaShowApp(),
    ),
  );
}

class BibliaShowApp extends StatelessWidget {
  const BibliaShowApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Bíblia Show 2.0',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        brightness: Brightness.dark,
        scaffoldBackgroundColor: AppColors.backgroundBottom,
        textTheme: GoogleFonts.interTextTheme(
          ThemeData(brightness: Brightness.dark).textTheme,
        ),
        colorScheme: const ColorScheme.dark(
          primary: AppColors.gold,
          secondary: Color(0xFF38BDF8),
          surface: AppColors.panelBgSolid,
        ),
      ),
      home: const HomeScreen(),
    );
  }
}
