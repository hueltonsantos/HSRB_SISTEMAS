import 'package:flutter/material.dart';

/// Cores do sistema HSRB_SISTEMAS
/// Replicando as cores do sistema web (#4e73df, #224abe)
class AppColors {
  AppColors._(); // Construtor privado para classe utilitária
  
  // ===== Cores Primárias (do CSS) =====
  
  /// Cor primária principal: #4e73df
  static const Color primary = Color(0xFF4E73DF);
  
  /// Cor primária escura: #224abe
  static const Color primaryDark = Color(0xFF224ABE);
  
  /// Cor primária clara: #3a5fc8 (usado em submenus)
  static const Color primaryLight = Color(0xFF3A5FC8);
  
  // ===== Cores de Status =====
  
  /// Verde de sucesso: #1cc88a
  static const Color success = Color(0xFF1CC88A);
  static const Color successDark = Color(0xFF13855C);
  
  /// Azul de informação: #36b9cc
  static const Color info = Color(0xFF36B9CC);
  static const Color infoDark = Color(0xFF258391);
  
  /// Amarelo de aviso: #f6c23e
  static const Color warning = Color(0xFFF6C23E);
  static const Color warningDark = Color(0xFFDDA20A);
  
  /// Vermelho de perigo: #e74a3b
  static const Color danger = Color(0xFFE74A3B);
  static const Color dangerDark = Color(0xFFBE2617);
  
  // ===== Escala de Cinza (Gray Scale) =====
  
  /// Cinza 100 (mais claro): #f8f9fc
  static const Color gray100 = Color(0xFFF8F9FC);
  
  /// Cinza 200: #eaecf4
  static const Color gray200 = Color(0xFFEAECF4);
  
  /// Cinza 300: #dddfeb
  static const Color gray300 = Color(0xFFDDDFEB);
  
  /// Cinza 400: #d1d3e2
  static const Color gray400 = Color(0xFFD1D3E2);
  
  /// Cinza 500: #b7b9cc
  static const Color gray500 = Color(0xFFB7B9CC);
  
  /// Cinza 600: #858796
  static const Color gray600 = Color(0xFF858796);
  
  /// Cinza 700: #6e707e
  static const Color gray700 = Color(0xFF6E707E);
  
  /// Cinza 800: #5a5c69
  static const Color gray800 = Color(0xFF5A5C69);
  
  /// Cinza 900 (mais escuro): #3a3b45
  static const Color gray900 = Color(0xFF3A3B45);
  
  // ===== Cores de Texto =====
  
  /// Texto primário (escuro)
  static const Color textPrimary = gray900;
  
  /// Texto secundário (médio)
  static const Color textSecondary = gray600;
  
  /// Texto claro (para fundos escuros)
  static const Color textLight = gray500;
  
  /// Texto muito claro
  static const Color textWhite = Colors.white;
  
  // ===== Cores de Fundo =====
  
  /// Fundo principal do app
  static const Color background = gray100;
  
  /// Fundo de cards e containers
  static const Color cardBackground = Colors.white;
  
  /// Cor de divisores
  static const Color divider = Color(0xFFE3E6F0);
  
  // ===== Gradientes =====
  
  /// Gradiente primário (usado em AppBar, botões, etc)
  static const LinearGradient primaryGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [primary, primaryDark],
  );
  
  /// Gradiente de sucesso
  static const LinearGradient successGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [success, successDark],
  );
  
  /// Gradiente de informação
  static const LinearGradient infoGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [info, infoDark],
  );
  
  /// Gradiente de aviso
  static const LinearGradient warningGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [warning, warningDark],
  );
  
  /// Gradiente de perigo
  static const LinearGradient dangerGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [danger, dangerDark],
  );
  
  // ===== Sombras =====
  
  /// Sombra padrão
  static const List<BoxShadow> defaultShadow = [
    BoxShadow(
      color: Color.fromRGBO(58, 59, 69, 0.15),
      blurRadius: 1.75,
      offset: Offset(0, 0.15),
    ),
  ];
  
  /// Sombra pequena
  static const List<BoxShadow> smallShadow = [
    BoxShadow(
      color: Color.fromRGBO(58, 59, 69, 0.2),
      blurRadius: 0.25,
      offset: Offset(0, 0.125),
    ),
  ];
  
  /// Sombra grande
  static const List<BoxShadow> largeShadow = [
    BoxShadow(
      color: Color.fromRGBO(0, 0, 0, 0.175),
      blurRadius: 3.0,
      offset: Offset(0, 1.0),
    ),
  ];
}
