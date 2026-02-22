import 'package:shared_preferences/shared_preferences.dart';

/// Gerenciador de armazenamento local
/// Salva tokens, preferências e dados do usuário
class StorageManager {
  static const String _keyToken = 'auth_token';
  static const String _keyRefreshToken = 'refresh_token';
  static const String _keyUserData = 'user_data';
  static const String _keyBiometricEnabled = 'biometric_enabled';
  static const String _keyRememberEmail = 'remember_email';

  final SharedPreferences _prefs;

  StorageManager(this._prefs);

  // ===== Token =====

  Future<void> saveToken(String token) async {
    await _prefs.setString(_keyToken, token);
  }

  String? getToken() {
    return _prefs.getString(_keyToken);
  }

  Future<void> saveRefreshToken(String refreshToken) async {
    await _prefs.setString(_keyRefreshToken, refreshToken);
  }

  String? getRefreshToken() {
    return _prefs.getString(_keyRefreshToken);
  }

  Future<void> clearTokens() async {
    await _prefs.remove(_keyToken);
    await _prefs.remove(_keyRefreshToken);
  }

  // ===== User Data =====

  Future<void> saveUserData(String userData) async {
    await _prefs.setString(_keyUserData, userData);
  }

  String? getUserData() {
    return _prefs.getString(_keyUserData);
  }

  Future<void> clearUserData() async {
    await _prefs.remove(_keyUserData);
  }

  // ===== Biometric =====

  Future<void> setBiometricEnabled(bool enabled) async {
    await _prefs.setBool(_keyBiometricEnabled, enabled);
  }

  bool isBiometricEnabled() {
    return _prefs.getBool(_keyBiometricEnabled) ?? false;
  }

  // ===== Remember Email =====

  Future<void> saveRememberEmail(String email) async {
    await _prefs.setString(_keyRememberEmail, email);
  }

  String? getRememberEmail() {
    return _prefs.getString(_keyRememberEmail);
  }

  Future<void> clearRememberEmail() async {
    await _prefs.remove(_keyRememberEmail);
  }

  // ===== Clear All =====

  Future<void> clearAll() async {
    await clearTokens();
    await clearUserData();
    // Mantém preferências de biometria e email
  }

  // ===== Helpers =====

  bool get isLoggedIn => getToken() != null;
}
