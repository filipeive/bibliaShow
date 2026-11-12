import 'package:audioplayers/audioplayers.dart';
import 'package:flutter/services.dart';
import 'package:shared_preferences/shared_preferences.dart';

class AudioService {
  static final AudioService instance = AudioService._internal();
  AudioService._internal();

  final AudioPlayer _fxPlayer = AudioPlayer();
  final AudioPlayer _bgPlayer = AudioPlayer();

  bool _isMuted = false;
  double _volume = 0.85;

  bool get isMuted => _isMuted;
  double get volume => _volume;

  Future<void> init() async {
    final prefs = await SharedPreferences.getInstance();
    _isMuted = prefs.getBool('biblia_show_som_mudo') ?? false;
    _volume = prefs.getDouble('biblia_show_volume') ?? 0.85;
    _fxPlayer.setVolume(_volume);
    _bgPlayer.setVolume(_volume * 0.4);
    _bgPlayer.setReleaseMode(ReleaseMode.loop);
  }

  Future<void> toggleMute() async {
    _isMuted = !_isMuted;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool('biblia_show_som_mudo', _isMuted);

    if (_isMuted) {
      await stopBackground();
      await _fxPlayer.stop();
    }
  }

  Future<void> playFx(String soundName) async {
    if (_isMuted) return;
    try {
      final player = AudioPlayer();
      await player.setVolume(_volume);
      await player.play(AssetSource('audio/$soundName.wav'));
      player.onPlayerComplete.listen((_) => player.dispose());
    } catch (_) {}
  }

  Future<void> playBackground(String soundName) async {
    if (_isMuted) return;
    try {
      await _bgPlayer.setVolume(_volume * 0.35);
      await _bgPlayer.play(AssetSource('audio/$soundName.wav'));
    } catch (_) {}
  }

  Future<void> stopBackground() async {
    try {
      await _bgPlayer.stop();
    } catch (_) {}
  }

  Future<void> vibrate([int durationMs = 40]) async {
    if (_isMuted) return;
    try {
      await HapticFeedback.selectionClick();
    } catch (_) {}
  }

  Future<void> vibrateSuccess() async {
    if (_isMuted) return;
    try {
      await HapticFeedback.mediumImpact();
      await Future.delayed(const Duration(milliseconds: 70));
      await HapticFeedback.mediumImpact();
    } catch (_) {}
  }

  Future<void> vibrateError() async {
    if (_isMuted) return;
    try {
      await HapticFeedback.heavyImpact();
      await Future.delayed(const Duration(milliseconds: 100));
      await HapticFeedback.heavyImpact();
    } catch (_) {}
  }
}
