# HSRB Mobile - Flutter App

Aplicativo mobile do Sistema de Atendimento Clínico HSRB_SISTEMAS desenvolvido em Flutter.

## 📱 Sobre o Projeto

Versão mobile completa do sistema web de gerenciamento clínico, replicando todas as funcionalidades e mantendo a mesma identidade visual.

### Funcionalidades

- ✅ **Autenticação** com JWT
- ✅ **Dashboard** com estatísticas em tempo real
- ✅ **Pacientes** - CRUD completo
- ✅ **Clínicas** - Gerenciamento de clínicas parceiras
- ✅ **Especialidades** - Cadastro de especialidades médicas
- ✅ **Agendamentos** - Sistema completo de agendamentos
- ✅ **Guias** - Emissão de guias de encaminhamento
- ✅ **Usuários** - Gerenciamento de usuários (admin)
- ✅ **Permissões** - Sistema de perfis e permissões
- ✅ **Relatórios** - Visualização e exportação de relatórios

### Recursos Mobile

- 🔔 **Notificações Push** via Firebase
- 🔒 **Biometria** para login rápido
- 📴 **Modo Offline** com sincronização automática
- 📷 **Câmera** para captura de documentos
- 📊 **Gráficos** interativos
- 🎨 **Design** idêntico ao sistema web

## 🚀 Começando

### Pré-requisitos

- Flutter SDK 3.0.0 ou superior
- Dart SDK 3.0.0 ou superior
- Android Studio / Xcode (para emuladores)
- Backend API rodando (veja `/api` no projeto principal)

### Instalação

1. Clone o repositório (já feito)

2. Instale as dependências:

```bash
cd hsrb_mobile
flutter pub get
```

3. Configure a URL da API:
   Edite `lib/core/constants/api_endpoints.dart` e altere `baseUrl`:

```dart
static const String baseUrl = 'http://SEU_IP:8080/api';
```

4. Execute o app:

```bash
# Android
flutter run

# iOS
flutter run -d ios

# Web (para testes)
flutter run -d chrome
```

## 🎨 Design System

### Cores

O app replica exatamente as cores do sistema web:

- **Primary**: `#4e73df`
- **Primary Dark**: `#224abe`
- **Primary Light**: `#3a5fc8`
- **Success**: `#1cc88a`
- **Info**: `#36b9cc`
- **Warning**: `#f6c23e`
- **Danger**: `#e74a3b`

### Fonte

- **Nunito** (mesma do sistema web)

## 📂 Estrutura do Projeto

```
lib/
├── core/
│   ├── constants/          # Constantes (cores, strings, endpoints)
│   ├── theme/              # Tema do app
│   ├── utils/              # Utilitários
│   └── errors/             # Tratamento de erros
├── data/
│   ├── models/             # Modelos de dados
│   ├── datasources/        # Fontes de dados (API, local)
│   └── repositories/       # Implementação de repositórios
├── domain/
│   ├── entities/           # Entidades de negócio
│   ├── repositories/       # Interfaces de repositórios
│   └── usecases/           # Casos de uso
└── presentation/
    ├── bloc/               # Gerenciamento de estado (BLoC)
    ├── pages/              # Telas do app
    └── widgets/            # Widgets reutilizáveis
```

## 🔧 Configuração do Backend

Certifique-se de que a API REST está rodando:

```bash
# No diretório do projeto principal
cd c:\xampp\htdocs\clinica_2026\HSRB_SISTEMAS

# Inicie o XAMPP (Apache + MySQL)
# Acesse: http://localhost:8080/api/auth/login
```

## 📱 Build para Produção

### Android

```bash
# APK
flutter build apk --release

# App Bundle (para Play Store)
flutter build appbundle --release
```

### iOS

```bash
flutter build ios --release
```

## 🧪 Testes

```bash
# Testes unitários
flutter test

# Testes de integração
flutter test integration_test/
```

## 📝 Credenciais de Teste

- **Email**: `hsrbsistemas@gmail.com`
- **Senha**: `123Mudar@`

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT.

## 👨‍💻 Autor

**Huelton dos Santos Ribeiro Borges**

- GitHub: [@hueltonsantos](https://github.com/hueltonsantos)
- LinkedIn: [huelton-santosdvs](https://www.linkedin.com/in/huelton-santosdvs)
- Email: hueltonti@gmail.com

---

Desenvolvido com ❤️ usando Flutter
