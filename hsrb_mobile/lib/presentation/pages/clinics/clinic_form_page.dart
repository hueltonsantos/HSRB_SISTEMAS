import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/constants/app_strings.dart';
import '../../../core/services/clinic_service.dart';
import '../../../data/models/clinic_model.dart';

class ClinicFormPage extends StatefulWidget {
  final ClinicModel? clinic;

  const ClinicFormPage({super.key, this.clinic});

  bool get isEditing => clinic != null;

  @override
  State<ClinicFormPage> createState() => _ClinicFormPageState();
}

class _ClinicFormPageState extends State<ClinicFormPage> {
  final _formKey = GlobalKey<FormState>();
  bool _isSaving = false;

  late final TextEditingController _nomeController;
  late final TextEditingController _razaoSocialController;
  late final TextEditingController _cnpjController;
  late final TextEditingController _responsavelController;
  late final TextEditingController _enderecoController;
  late final TextEditingController _numeroController;
  late final TextEditingController _complementoController;
  late final TextEditingController _bairroController;
  late final TextEditingController _cidadeController;
  late final TextEditingController _cepController;
  late final TextEditingController _telefoneController;
  late final TextEditingController _celularController;
  late final TextEditingController _emailController;
  late final TextEditingController _siteController;

  String? _selectedEstado;

  final List<String> _estados = [
    'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
    'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN',
    'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
  ];

  @override
  void initState() {
    super.initState();
    final c = widget.clinic;
    _nomeController = TextEditingController(text: c?.nome ?? '');
    _razaoSocialController = TextEditingController(text: c?.razaoSocial ?? '');
    _cnpjController = TextEditingController(text: c?.cnpj ?? '');
    _responsavelController = TextEditingController(text: c?.responsavel ?? '');
    _enderecoController = TextEditingController(text: c?.endereco ?? '');
    _numeroController = TextEditingController(text: c?.numero ?? '');
    _complementoController = TextEditingController(text: c?.complemento ?? '');
    _bairroController = TextEditingController(text: c?.bairro ?? '');
    _cidadeController = TextEditingController(text: c?.cidade ?? '');
    _cepController = TextEditingController(text: c?.cep ?? '');
    _telefoneController = TextEditingController(text: c?.telefone ?? '');
    _celularController = TextEditingController(text: c?.celular ?? '');
    _emailController = TextEditingController(text: c?.email ?? '');
    _siteController = TextEditingController(text: c?.site ?? '');
    _selectedEstado = c?.estado;
  }

  @override
  void dispose() {
    _nomeController.dispose();
    _razaoSocialController.dispose();
    _cnpjController.dispose();
    _responsavelController.dispose();
    _enderecoController.dispose();
    _numeroController.dispose();
    _complementoController.dispose();
    _bairroController.dispose();
    _cidadeController.dispose();
    _cepController.dispose();
    _telefoneController.dispose();
    _celularController.dispose();
    _emailController.dispose();
    _siteController.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSaving = true);

    try {
      final service = context.read<ClinicService>();

      final data = <String, dynamic>{
        'nome': _nomeController.text.trim(),
        'endereco': _enderecoController.text.trim(),
        'cidade': _cidadeController.text.trim(),
        'estado': _selectedEstado ?? '',
        'telefone': _telefoneController.text.trim(),
      };

      if (_razaoSocialController.text.trim().isNotEmpty) data['razao_social'] = _razaoSocialController.text.trim();
      if (_cnpjController.text.trim().isNotEmpty) data['cnpj'] = _cnpjController.text.trim();
      if (_responsavelController.text.trim().isNotEmpty) data['responsavel'] = _responsavelController.text.trim();
      if (_numeroController.text.trim().isNotEmpty) data['numero'] = _numeroController.text.trim();
      if (_complementoController.text.trim().isNotEmpty) data['complemento'] = _complementoController.text.trim();
      if (_bairroController.text.trim().isNotEmpty) data['bairro'] = _bairroController.text.trim();
      if (_cepController.text.trim().isNotEmpty) data['cep'] = _cepController.text.trim();
      if (_celularController.text.trim().isNotEmpty) data['celular'] = _celularController.text.trim();
      if (_emailController.text.trim().isNotEmpty) data['email'] = _emailController.text.trim();
      if (_siteController.text.trim().isNotEmpty) data['site'] = _siteController.text.trim();

      if (widget.isEditing) {
        await service.updateClinic(widget.clinic!.id, data);
      } else {
        await service.createClinic(data);
      }

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(widget.isEditing ? AppStrings.updateSuccess : AppStrings.saveSuccess),
            backgroundColor: AppColors.success,
          ),
        );
        Navigator.pop(context, true);
      }
    } catch (e) {
      setState(() => _isSaving = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(e.toString().replaceAll('Exception: ', '')),
            backgroundColor: AppColors.danger,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.isEditing ? 'Editar Clinica' : 'Nova Clinica'),
      ),
      body: Form(
        key: _formKey,
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              _buildSectionHeader('Dados da Clinica', Icons.business),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _nomeController,
                label: 'Nome Fantasia',
                icon: Icons.business,
                required: true,
                textCapitalization: TextCapitalization.words,
              ),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _razaoSocialController,
                label: 'Razao Social',
                icon: Icons.account_balance,
                textCapitalization: TextCapitalization.words,
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: _buildTextField(
                      controller: _cnpjController,
                      label: 'CNPJ',
                      icon: Icons.badge,
                      keyboardType: TextInputType.number,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: _buildTextField(
                      controller: _responsavelController,
                      label: 'Responsavel',
                      icon: Icons.person,
                      textCapitalization: TextCapitalization.words,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 24),

              _buildSectionHeader('Contato', Icons.contact_phone),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: _buildTextField(
                      controller: _telefoneController,
                      label: AppStrings.phone,
                      icon: Icons.phone,
                      required: true,
                      keyboardType: TextInputType.phone,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: _buildTextField(
                      controller: _celularController,
                      label: 'Celular',
                      icon: Icons.phone_android,
                      keyboardType: TextInputType.phone,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _emailController,
                label: AppStrings.email,
                icon: Icons.email,
                keyboardType: TextInputType.emailAddress,
              ),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _siteController,
                label: 'Site',
                icon: Icons.language,
                keyboardType: TextInputType.url,
              ),
              const SizedBox(height: 24),

              _buildSectionHeader(AppStrings.address, Icons.location_on),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _cepController,
                label: AppStrings.zipCode,
                icon: Icons.pin_drop,
                keyboardType: TextInputType.number,
              ),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _enderecoController,
                label: 'Logradouro',
                icon: Icons.home,
                required: true,
                textCapitalization: TextCapitalization.words,
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    flex: 2,
                    child: _buildTextField(
                      controller: _numeroController,
                      label: 'Numero',
                      icon: Icons.numbers,
                      keyboardType: TextInputType.number,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    flex: 3,
                    child: _buildTextField(
                      controller: _complementoController,
                      label: 'Complemento',
                      icon: Icons.add_location,
                      textCapitalization: TextCapitalization.sentences,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _bairroController,
                label: 'Bairro',
                icon: Icons.location_city,
                textCapitalization: TextCapitalization.words,
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    flex: 3,
                    child: _buildTextField(
                      controller: _cidadeController,
                      label: AppStrings.city,
                      icon: Icons.location_city,
                      required: true,
                      textCapitalization: TextCapitalization.words,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    flex: 2,
                    child: DropdownButtonFormField<String>(
                      value: _selectedEstado,
                      decoration: InputDecoration(
                        labelText: '${AppStrings.state} *',
                        prefixIcon: const Icon(Icons.map),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: const BorderSide(color: AppColors.gray300),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: const BorderSide(color: AppColors.primary, width: 2),
                        ),
                        filled: true,
                        fillColor: Colors.white,
                      ),
                      isExpanded: true,
                      items: _estados
                          .map((e) => DropdownMenuItem(value: e, child: Text(e)))
                          .toList(),
                      onChanged: (v) => setState(() => _selectedEstado = v),
                      validator: (v) {
                        if (v == null || v.isEmpty) return AppStrings.requiredField;
                        return null;
                      },
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 32),

              SizedBox(
                height: 50,
                child: ElevatedButton(
                  onPressed: _isSaving ? null : _save,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    elevation: 2,
                  ),
                  child: _isSaving
                      ? const SizedBox(
                          height: 24,
                          width: 24,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            valueColor: AlwaysStoppedAnimation(Colors.white),
                          ),
                        )
                      : Text(
                          widget.isEditing ? AppStrings.save : 'Nova Clinica',
                          style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
                        ),
                ),
              ),
              const SizedBox(height: 32),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSectionHeader(String title, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 20, color: AppColors.primary),
        const SizedBox(width: 8),
        Text(
          title,
          style: const TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.w700,
            color: AppColors.textPrimary,
          ),
        ),
      ],
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    bool required = false,
    TextInputType? keyboardType,
    List<TextInputFormatter>? inputFormatters,
    int maxLines = 1,
    TextCapitalization textCapitalization = TextCapitalization.none,
  }) {
    return TextFormField(
      controller: controller,
      decoration: InputDecoration(
        labelText: required ? '$label *' : label,
        prefixIcon: Icon(icon),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.gray300),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.primary, width: 2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.danger),
        ),
        filled: true,
        fillColor: Colors.white,
      ),
      keyboardType: keyboardType,
      inputFormatters: inputFormatters,
      maxLines: maxLines,
      textCapitalization: textCapitalization,
      validator: required
          ? (v) {
              if (v == null || v.trim().isEmpty) return AppStrings.requiredField;
              return null;
            }
          : null,
    );
  }
}
