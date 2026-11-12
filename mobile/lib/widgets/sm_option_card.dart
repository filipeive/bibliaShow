import 'package:flutter/material.dart';
import '../core/constants/app_colors.dart';

enum OptionState { idle, selected, correct, wrong, eliminated }

class SmOptionCard extends StatelessWidget {
  final String letter;
  final String text;
  final OptionState state;
  final VoidCallback? onTap;

  const SmOptionCard({
    super.key,
    required this.letter,
    required this.text,
    this.state = OptionState.idle,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    if (state == OptionState.eliminated) {
      return Opacity(
        opacity: 0.3,
        child: Container(
          margin: const EdgeInsets.symmetric(vertical: 5),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          decoration: BoxDecoration(
            color: const Color(0xFF030712),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: Colors.white12),
          ),
          child: Row(
            children: [
              _buildLetterBadge(letter, isEliminated: true),
              const SizedBox(width: 14),
              Expanded(
                child: Text(
                  text,
                  style: const TextStyle(
                    color: Colors.white38,
                    fontSize: 15,
                    decoration: TextDecoration.lineThrough,
                  ),
                ),
              ),
              const Text(
                '50/50',
                style: TextStyle(color: AppColors.wrongRed, fontSize: 11, fontWeight: FontWeight.bold),
              ),
            ],
          ),
        ),
      );
    }

    Color bgColor;
    Color borderColor;
    Color letterBg;
    Color letterTextColor = Colors.black;

    switch (state) {
      case OptionState.selected:
        bgColor = AppColors.optionBlockedBg;
        borderColor = AppColors.gold;
        letterBg = AppColors.gold;
        break;
      case OptionState.correct:
        bgColor = AppColors.correctGreenBg;
        borderColor = AppColors.correctGreen;
        letterBg = AppColors.correctGreen;
        break;
      case OptionState.wrong:
        bgColor = AppColors.wrongRedBg;
        borderColor = AppColors.wrongRed;
        letterBg = AppColors.wrongRed;
        letterTextColor = Colors.white;
        break;
      case OptionState.idle:
      default:
        bgColor = AppColors.optionBg;
        borderColor = AppColors.optionBorder;
        letterBg = AppColors.gold;
        break;
    }

    return Container(
      margin: const EdgeInsets.symmetric(vertical: 5),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(16),
          onTap: onTap,
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 200),
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            decoration: BoxDecoration(
              color: bgColor,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: borderColor, width: state != OptionState.idle ? 2.5 : 1.5),
              boxShadow: state == OptionState.selected || state == OptionState.correct
                  ? [
                      BoxShadow(
                        color: borderColor.withOpacity(0.35),
                        blurRadius: 15,
                        spreadRadius: 1,
                      ),
                    ]
                  : [],
            ),
            child: Row(
              children: [
                _buildLetterBadge(letter, customBg: letterBg, customText: letterTextColor),
                const SizedBox(width: 14),
                Expanded(
                  child: Text(
                    text,
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 15,
                      fontWeight: FontWeight.w700,
                      height: 1.25,
                    ),
                  ),
                ),
                if (state == OptionState.correct)
                  const Icon(Icons.check_circle, color: AppColors.correctGreen, size: 22)
                else if (state == OptionState.wrong)
                  const Icon(Icons.cancel, color: AppColors.wrongRed, size: 22)
                else if (state == OptionState.selected)
                  const SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(
                      strokeWidth: 2.5,
                      color: AppColors.gold,
                    ),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildLetterBadge(String letter, {bool isEliminated = false, Color? customBg, Color? customText}) {
    return Container(
      width: 36,
      height: 36,
      decoration: BoxDecoration(
        gradient: isEliminated
            ? null
            : (customBg == null
                ? AppColors.goldGradient
                : LinearGradient(colors: [customBg, customBg.withOpacity(0.85)])),
        color: isEliminated ? Colors.white10 : null,
        borderRadius: BorderRadius.circular(10),
        border: Border.all(
          color: isEliminated ? Colors.white12 : Colors.white.withOpacity(0.3),
        ),
        boxShadow: isEliminated
            ? []
            : [
                BoxShadow(
                  color: Colors.black.withOpacity(0.4),
                  blurRadius: 6,
                  offset: const Offset(0, 2),
                ),
              ],
      ),
      alignment: Alignment.center,
      child: Text(
        letter,
        style: TextStyle(
          color: isEliminated ? Colors.white38 : (customText ?? Colors.black),
          fontSize: 16,
          fontWeight: FontWeight.w900,
        ),
      ),
    );
  }
}
