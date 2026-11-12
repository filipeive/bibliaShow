import 'package:flutter/material.dart';

class AppColors {
  // Cores do Palco Show do Milhão
  static const Color backgroundTop = Color(0xFF0e2169);
  static const Color backgroundMiddle = Color(0xFF071138);
  static const Color backgroundBottom = Color(0xFF030718);

  // Painéis e Cards
  static const Color panelBg = Color(0xCC091642);
  static const Color panelBorder = Color(0x4D38BDF8); // blue-400/30
  static const Color panelBgSolid = Color(0xFF091642);

  // Dourado Show do Milhão
  static const Color goldLight = Color(0xFFFDE68A); // amber-200
  static const Color gold = Color(0xFFF59E0B);      // amber-500
  static const Color goldDark = Color(0xFFB45309);  // amber-700
  static const Color goldBorder = Color(0x99F59E0B);

  // Enunciado Vinho / Vermelho Clássico
  static const Color questionBgTop = Color(0xFF450A0A);   // red-950
  static const Color questionBgMiddle = Color(0xFF7F1D1D); // red-900
  static const Color questionBgBottom = Color(0xFF450A0A);

  // Feedback de Resposta
  static const Color correctGreen = Color(0xFF10B981);
  static const Color correctGreenBg = Color(0xFF064E3B);
  static const Color wrongRed = Color(0xFFF43F5E);
  static const Color wrongRedBg = Color(0xFF4C0519);

  // Alternativas (Cards Safira)
  static const Color optionBg = Color(0xD90F1E4A);
  static const Color optionBorder = Color(0x4D60A5FA);
  static const Color optionBlockedBg = Color(0x9978350F);

  // Gradientes
  static const LinearGradient stageGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [backgroundTop, backgroundMiddle, backgroundBottom],
  );

  static const LinearGradient goldGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [goldLight, gold, goldDark],
  );

  static const LinearGradient questionGradient = LinearGradient(
    begin: Alignment.centerLeft,
    end: Alignment.centerRight,
    colors: [questionBgTop, questionBgMiddle, questionBgBottom],
  );
}
