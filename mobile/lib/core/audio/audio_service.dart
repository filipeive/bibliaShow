import 'package:audioplayers/audioplayers.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/services.dart';
import 'package:shared_preferences/shared_preferences.dart';

class AudioService {
  static final AudioService instance = AudioService._internal();
  AudioService._internal();

  // Players dedicados para evitar concorrência e vazamento de AudioTracks no Android
  final AudioPlayer _bgPlayer = AudioPlayer();
  final AudioPlayer _fxPlayer = AudioPlayer();
  final AudioPlayer _tensionPlayer = AudioPlayer();
  final AudioPlayer _verdictPlayer = AudioPlayer();
  final AudioPlayer _timerPlayer = AudioPlayer();

  bool _isMuted = false;
  double _volume = 0.85;
  String? _currentBgTrack;

  bool get isMuted => _isMuted;
  double get volume => _volume;

  Future<void> init() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      _isMuted = prefs.getBool('biblia_show_som_mudo') ?? false;
      _volume = prefs.getDouble('biblia_show_volume') ?? 0.85;

      await _configurarPlayers();
    } catch (e) {
      debugPrint('Erro ao inicializar AudioService: $e');
    }
  }

  Future<void> _configurarPlayers() async {
    final effectiveVol = _isMuted ? 0.0 : _volume;
    await _bgPlayer.setVolume(effectiveVol * 0.32);
    await _bgPlayer.setReleaseMode(ReleaseMode.loop);

    await _fxPlayer.setVolume(effectiveVol);
    await _tensionPlayer.setVolume(effectiveVol * 0.95);
    await _verdictPlayer.setVolume(effectiveVol);
    await _timerPlayer.setVolume(effectiveVol * 0.7);
  }

  Future<void> setVolume(double vol) async {
    _volume = vol.clamp(0.0, 1.0);
    final prefs = await SharedPreferences.getInstance();
    await prefs.setDouble('biblia_show_volume', _volume);
    await _configurarPlayers();
  }

  Future<void> toggleMute() async {
    _isMuted = !_isMuted;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool('biblia_show_som_mudo', _isMuted);

    if (_isMuted) {
      await stopAll();
    } else {
      await _configurarPlayers();
      if (_currentBgTrack != null) {
        await playBackground(_currentBgTrack!);
      }
    }
  }

  /// Toca trilha contínua de fundo em loop (abertura ou suspense)
  Future<void> playBackground(String soundName, {double factor = 0.32}) async {
    _currentBgTrack = soundName;
    if (_isMuted) return;

    try {
      // Se já está tocando exatamente a mesma faixa em loop, não reinicia bruscamente
      if (_bgPlayer.state == PlayerState.playing && _currentBgTrack == soundName) {
        await _bgPlayer.setVolume(_volume * factor);
        return;
      }

      await _bgPlayer.stop();
      await _bgPlayer.setReleaseMode(ReleaseMode.loop);
      await _bgPlayer.setVolume(_volume * factor);
      await _bgPlayer.play(AssetSource('audio/$soundName.wav'));
    } catch (e) {
      debugPrint('Erro ao reproduzir background ($soundName): $e');
    }
  }

  Future<void> stopBackground() async {
    try {
      _currentBgTrack = null;
      await _bgPlayer.stop();
    } catch (_) {}
  }

  /// Toca efeitos sonoros curtos e ágeis (tecla, ajuda, dica, início)
  Future<void> playFx(String soundName, {double factor = 1.0}) async {
    if (_isMuted) return;
    try {
      await _fxPlayer.stop();
      await _fxPlayer.setVolume((_volume * factor).clamp(0.0, 1.0));
      await _fxPlayer.play(AssetSource('audio/$soundName.wav'));
    } catch (e) {
      debugPrint('Erro ao tocar FX ($soundName): $e');
    }
  }

  /// Toca suspense e tensão ao selecionar uma resposta ("Você tem certeza?")
  Future<void> playTension({int variante = 1}) async {
    if (_isMuted) return;
    try {
      final name = 'sabe${(variante % 3) + 1}';
      await _tensionPlayer.stop();
      await _tensionPlayer.setVolume(_volume * 0.95);
      await _tensionPlayer.play(AssetSource('audio/$name.wav'));
    } catch (e) {
      debugPrint('Erro ao tocar tensão: $e');
    }
  }

  /// Toca efeito de cronômetro (tick, alerta ou esgotado)
  Future<void> playTimerTick(int segundosRestantes) async {
    if (_isMuted) return;
    try {
      if (segundosRestantes > 10 || segundosRestantes < 0) return;
      await _timerPlayer.stop();
      if (segundosRestantes > 0) {
        final track = segundosRestantes <= 3 ? 'tempo_alerta' : 'tempo_tick';
        await _timerPlayer.setVolume(_volume * 0.75);
        await _timerPlayer.play(AssetSource('audio/$track.wav'));
      } else {
        await _timerPlayer.setVolume(_volume * 0.85);
        await _timerPlayer.play(AssetSource('audio/ks.wav'));
      }
    } catch (_) {}
  }

  /// Toca veredito final da resposta (certa ou errou com áudio clássico)
  Future<void> playVerdict({required bool isCorrect, int fase = 1}) async {
    if (_isMuted) return;
    try {
      final numSom = ((fase - 1) % 3) + 1;
      final track = isCorrect ? 'certa$numSom' : 'errou$numSom';
      await _verdictPlayer.stop();
      await _verdictPlayer.setVolume(_volume);
      await _verdictPlayer.play(AssetSource('audio/$track.wav'));
    } catch (e) {
      debugPrint('Erro ao tocar veredito: $e');
    }
  }

  /// Toca vinheta de fase (fase1, fase2, fase3, fase4)
  Future<void> playFase(int fase) async {
    if (_isMuted) return;
    try {
      final track = 'fase${fase.clamp(1, 4)}';
      await _verdictPlayer.stop();
      await _verdictPlayer.setVolume(_volume * 0.9);
      await _verdictPlayer.play(AssetSource('audio/$track.wav'));
    } catch (_) {}
  }

  /// Para todos os sons
  Future<void> stopAll() async {
    try {
      _currentBgTrack = null;
      await _bgPlayer.stop();
      await _fxPlayer.stop();
      await _tensionPlayer.stop();
      await _verdictPlayer.stop();
      await _timerPlayer.stop();
    } catch (_) {}
  }

  // Feedback Háptico Tátil Nativo
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
