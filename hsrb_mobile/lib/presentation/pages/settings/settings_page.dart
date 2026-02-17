import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../bloc/settings_bloc.dart';
import '../../../data/repositories/settings_repository.dart';
import '../../../core/services/auth_service.dart';

/// Página de Configurações do Sistema
class SettingsPage extends StatelessWidget {
  const SettingsPage({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => SettingsBloc(
        repository: SettingsRepository(authService: context.read<AuthService>()),
      )..add(LoadSettings()),
      child: const SettingsView(),
    );
  }
}

class SettingsView extends StatefulWidget {
  const SettingsView({Key? key}) : super(key: key);

  @override
  State<SettingsView> createState() => _SettingsViewState();
}

class _SettingsViewState extends State<SettingsView> {
  // Local state to hold form data before saving
  final Map<String, dynamic> _formData = {};
  bool _isInit = true;

  @override
  Widget build(BuildContext context) {
    return BlocConsumer<SettingsBloc, SettingsState>(
      listener: (context, state) {
         if (state is SettingsUpdateSuccess) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(state.message), backgroundColor: Colors.green),
          );
        } else if (state is SettingsError) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(state.message), backgroundColor: Colors.red),
          );
        } else if (state is SettingsLoaded && _isInit) {
           // Initialize form data only once effectively
           _formData.addAll(state.settings);
           _isInit = false;
        }
      },
      builder: (context, state) {
        if (state is SettingsLoading) {
           return const Scaffold(
             body: Center(child: CircularProgressIndicator()),
           );
        }
        
        // We can display form even if loading updates, capturing taps
        return Scaffold(
          appBar: AppBar(
            title: const Text('Configurações'),
            actions: [
              IconButton(
                icon: const Icon(Icons.save),
                onPressed: () {
                  context.read<SettingsBloc>().add(UpdateSettings(_formData));
                },
              ),
            ],
          ),
          body: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                _buildSection(
                  'Informações da Clínica',
                  [
                    _buildTextField('Nome da Clínica', 'nome_clinica'),
                    _buildTextField('Telefone', 'telefone_clinica'),
                    _buildTextField('Email', 'email_clinica'),
                    _buildTextField('Endereço', 'endereco_clinica'),
                  ],
                ),
                const SizedBox(height: 24),
                _buildSection(
                  'Configurações do Sistema',
                  [
                    _buildSwitchTile(
                      'Notificações por Email',
                      'notificacoes_email',
                    ),
                    _buildSwitchTile(
                      'Notificações por SMS',
                      'notificacoes_sms',
                    ),
                     _buildTextField('Dias para retorno', 'dias_retorno', keyboardType: TextInputType.number),
                  ],
                ),
              ],
            ),
        );
      },
    );
  }

  Widget _buildSection(String title, List<Widget> children) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              title,
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            const Divider(),
            ...children,
          ],
        ),
      ),
    );
  }

  Widget _buildTextField(String label, String key, {TextInputType? keyboardType}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: TextFormField(
        decoration: InputDecoration(
          labelText: label,
          border: const OutlineInputBorder(),
        ),
        // Use a Key to ensure re-render updates if needed, though controller is better for preserving focus
        // For simplicity with map sync, initialValue works if we don't need reset without reload
        initialValue: _formData[key]?.toString() ?? '',
        keyboardType: keyboardType,
        onChanged: (value) {
          _formData[key] = value;
        },
      ),
    );
  }

  Widget _buildSwitchTile(String title, String key) {
    // Handle API returning 1/0 or true/false
    bool getValue() {
      final val = _formData[key];
      if (val is bool) return val;
      if (val is int) return val == 1;
      if (val is String) return val == '1' || val == 'true';
      return false;
    }

    return SwitchListTile(
      title: Text(title),
      value: getValue(),
      onChanged: (value) {
        setState(() {
          _formData[key] = value ? 1 : 0; // Standardize on 1/0 for PHP
        });
      },
    );
  }
}
