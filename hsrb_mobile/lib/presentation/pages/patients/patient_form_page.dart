import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/constants/app_strings.dart';
import '../../../core/services/patient_service.dart';
import '../../../data/models/patient_model.dart';

class PatientFormPage extends StatefulWidget {
  final PatientModel? patient;

  const PatientFormPage({super.key, this.patient});

  bool get isEditing => patient != null;

  @override
  State<PatientFormPage> createState() => _PatientFormPageState();
}

class _PatientFormPageState extends State<PatientFormPage> {
  final _formKey = GlobalKey<FormState>();
  bool _isSaving = false;

  late final TextEditingController _nomeController;
  late final TextEditingController _cpfController;
  late final TextEditingController _rgController;
  late final TextEditingController _dataNascimentoController;
  late final TextEditingController _telefoneController;
  late final TextEditingController _emailController;
  late final TextEditingController _enderecoController;
  late final TextEditingController _numeroController;
  late final TextEditingController _complementoController;
  late final TextEditingController _bairroController;
  late final TextEditingController _cidadeController;
  late final TextEditingController _cepController;
  late final TextEditingController _nomeResponsavelController;
  late final TextEditingController _telefoneResponsavelController;
  late final TextEditingController _observacoesController;

  String? _selectedSexo;
  String? _selectedEstado;

  final List<String> _estados = [
    'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
    'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN',
    'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
  ];

  @override
  void initState() {
    super.initState();
    final p = widget.patient;
    _nomeController = TextEditingController(text: p?.nome ?? '');
    _cpfController = TextEditingController(text: p?.cpf ?? '');
    _rgController = TextEditingController(text: p?.rg ?? '');
    _dataNascimentoController = TextEditingController(text: p != null ? _formatDateForDisplay(p.dataNascimento) : '');
    _telefoneController = TextEditingController(text: p?.telefone ?? '');
    _emailController = TextEditingController(text: p?.email ?? '');
    _enderecoController = TextEditingController(text: p?.endereco ?? '');
    _numeroController = TextEditingController(text: p?.numero ?? '');
    _complementoController = TextEditingController(text: p?.complemento ?? '');
    _bairroController = TextEditingController(text: p?.bairro ?? '');
    _cidadeController = TextEditingController(text: p?.cidade ?? '');
    _cepController = TextEditingController(text: p?.cep ?? '');
    _nomeResponsavelController = TextEditingController(text: p?.nomeResponsavel ?? '');
    _telefoneResponsavelController = TextEditingController(text: p?.telefoneResponsavel ?? '');
    _observacoesController = TextEditingController(text: p?.observacoes ?? '');
    _selectedSexo = p?.sexo;
    _selectedEstado = p?.estado;
  }

  @override
  void dispose() {
    _nomeController.dispose();
    _cpfController.dispose();
    _rgController.dispose();
    _dataNascimentoController.dispose();
    _telefoneController.dispose();
    _emailController.dispose();
    _enderecoController.dispose();
    _numeroController.dispose();
    _complementoController.dispose();
    _bairroController.dispose();
    _cidadeController.dispose();
    _cepController.dispose();
    _nomeResponsavelController.dispose();
    _telefoneResponsavelController.dispose();
    _observacoesController.dispose();
    super.dispose();
  }

  Future<void> _selectDate() async {
    final now = DateTime.now();
    final picked = await showDatePicker(
      context: context,
      initialDate: _parseDateFromDisplay(_dataNascimentoController.text) ?? DateTime(2000),
      firstDate: DateTime(1900),
      lastDate: now,
      locale: const Locale('pt', 'BR'),
    );

    if (picked != null) {
      _dataNascimentoController.text =
          '${picked.day.toString().padLeft(2, '0')}/${picked.month.toString().padLeft(2, '0')}/${picked.year}';
    }
  }

  DateTime? _parseDateFromDisplay(String text) {
    try {
      final parts = text.split('/');
      if (parts.length == 3) {
        return DateTime(int.parse(parts[2]), int.parse(parts[1]), int.parse(parts[0]));
      }
    } catch (_) {}
    return null;
  }

  String _formatDateForApi(String displayDate) {
    try {
      final parts = displayDate.split('/');
      if (parts.length == 3) {
        return '${parts[2]}-${parts[1]}-${parts[0]}';
      }
    } catch (_) {}
    return displayDate;
  }

  String _formatDateForDisplay(String apiDate) {
    try {
      final parts = apiDate.split('-');
      if (parts.length == 3) {
        return '${parts[2]}/${parts[1]}/${parts[0]}';
      }
    } catch (_) {}
    return apiDate;
  }

  String _cleanMask(String text) {
    return text.replaceAll(RegExp(r'[^\d]'), '');
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSaving = true);

    try {
      final service = context.read<PatientService>();

      final data = <String, dynamic>{
        'nome': _nomeController.text.trim(),
        'cpf': _cleanMask(_cpfController.text),
        'data_nascimento': _formatDateForApi(_dataNascimentoController.text),
        'telefone': _cleanMask(_telefoneController.text),
      };

      if (_rgController.text.trim().isNotEmpty) data['rg'] = _rgController.text.trim();
      if (_selectedSexo != null) data['sexo'] = _selectedSexo;
      if (_emailController.text.trim().isNotEmpty) data['email'] = _emailController.text.trim();
      if (_enderecoController.text.trim().isNotEmpty) data['endereco'] = _enderecoController.text.trim();
      if (_numeroController.text.trim().isNotEmpty) data['numero'] = _numeroController.text.trim();
      if (_complementoController.text.trim().isNotEmpty) data['complemento'] = _complementoController.text.trim();
      if (_bairroController.text.trim().isNotEmpty) data['bairro'] = _bairroController.text.trim();
      if (_cidadeController.text.trim().isNotEmpty) data['cidade'] = _cidadeController.text.trim();
      if (_selectedEstado != null) data['estado'] = _selectedEstado;
      if (_cepController.text.trim().isNotEmpty) data['cep'] = _cleanMask(_cepController.text);
      if (_nomeResponsavelController.text.trim().isNotEmpty) data['nome_responsavel'] = _nomeResponsavelController.text.trim();
      if (_telefoneResponsavelController.text.trim().isNotEmpty) data['telefone_responsavel'] = _cleanMask(_telefoneResponsavelController.text);
      if (_observacoesController.text.trim().isNotEmpty) data['observacoes'] = _observacoesController.text.trim();

      if (widget.isEditing) {
        await service.updatePatient(widget.patient!.id, data);
      } else {
        await service.createPatient(data);
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
        title: Text(widget.isEditing ? AppStrings.editPatient : AppStrings.newPatient),
      ),
      body: Form(
        key: _formKey,
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Dados pessoais
              _buildSectionHeader('Dados Pessoais', Icons.person),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _nomeController,
                label: AppStrings.patientName,
                icon: Icons.person,
                required: true,
                textCapitalization: TextCapitalization.words,
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: _buildTextField(
                      controller: _cpfController,
                      label: AppStrings.cpf,
                      icon: Icons.badge,
                      required: true,
                      keyboardType: TextInputType.number,
                      inputFormatters: [
                        FilteringTextInputFormatter.digitsOnly,
                        _CpfInputFormatter(),
                      ],
                      validator: _validateCpf,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: _buildTextField(
                      controller: _rgController,
                      label: AppStrings.rg,
                      icon: Icons.credit_card,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: _buildTextField(
                      controller: _dataNascimentoController,
                      label: AppStrings.birthDate,
                      icon: Icons.calendar_today,
                      required: true,
                      readOnly: true,
                      onTap: _selectDate,
                      keyboardType: TextInputType.number,
                      inputFormatters: [
                        FilteringTextInputFormatter.digitsOnly,
                        _DateInputFormatter(),
                      ],
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      value: _selectedSexo,
                      decoration: InputDecoration(
                        labelText: AppStrings.gender,
                        prefixIcon: const Icon(Icons.wc),
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
                      items: const [
                        DropdownMenuItem(value: 'M', child: Text('Masculino')),
                        DropdownMenuItem(value: 'F', child: Text('Feminino')),
                      ],
                      onChanged: (v) => setState(() => _selectedSexo = v),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 24),

              // Contato
              _buildSectionHeader('Contato', Icons.contact_phone),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _telefoneController,
                label: AppStrings.phone,
                icon: Icons.phone,
                required: true,
                keyboardType: TextInputType.phone,
                inputFormatters: [
                  FilteringTextInputFormatter.digitsOnly,
                  _PhoneInputFormatter(),
                ],
              ),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _emailController,
                label: AppStrings.email,
                icon: Icons.email,
                keyboardType: TextInputType.emailAddress,
              ),
              const SizedBox(height: 24),

              // Endereco
              _buildSectionHeader(AppStrings.address, Icons.location_on),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _cepController,
                label: AppStrings.zipCode,
                icon: Icons.pin_drop,
                keyboardType: TextInputType.number,
                inputFormatters: [
                  FilteringTextInputFormatter.digitsOnly,
                  _CepInputFormatter(),
                ],
              ),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _enderecoController,
                label: 'Logradouro',
                icon: Icons.home,
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
                      textCapitalization: TextCapitalization.words,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    flex: 2,
                    child: DropdownButtonFormField<String>(
                      value: _selectedEstado,
                      decoration: InputDecoration(
                        labelText: AppStrings.state,
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
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 24),

              // Responsavel
              _buildSectionHeader('Responsavel', Icons.family_restroom),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _nomeResponsavelController,
                label: AppStrings.guardianName,
                icon: Icons.person_outline,
                textCapitalization: TextCapitalization.words,
              ),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _telefoneResponsavelController,
                label: AppStrings.guardianPhone,
                icon: Icons.phone_android,
                keyboardType: TextInputType.phone,
                inputFormatters: [
                  FilteringTextInputFormatter.digitsOnly,
                  _PhoneInputFormatter(),
                ],
              ),
              const SizedBox(height: 24),

              // Observacoes
              _buildSectionHeader(AppStrings.observations, Icons.notes),
              const SizedBox(height: 12),
              _buildTextField(
                controller: _observacoesController,
                label: AppStrings.observations,
                icon: Icons.notes,
                maxLines: 4,
                textCapitalization: TextCapitalization.sentences,
              ),
              const SizedBox(height: 32),

              // Botao salvar
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
                          widget.isEditing ? AppStrings.save : AppStrings.newPatient,
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
    bool readOnly = false,
    VoidCallback? onTap,
    TextCapitalization textCapitalization = TextCapitalization.none,
    String? Function(String?)? validator,
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
      readOnly: readOnly,
      onTap: onTap,
      textCapitalization: textCapitalization,
      validator: validator ??
          (required
              ? (v) {
                  if (v == null || v.trim().isEmpty) return AppStrings.requiredField;
                  return null;
                }
              : null),
    );
  }

  String? _validateCpf(String? value) {
    if (value == null || value.isEmpty) return AppStrings.requiredField;
    final digits = value.replaceAll(RegExp(r'[^\d]'), '');
    if (digits.length != 11) return AppStrings.invalidCpf;
    return null;
  }
}

// === Input Formatters ===

class _CpfInputFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(
      TextEditingValue oldValue, TextEditingValue newValue) {
    final digits = newValue.text.replaceAll(RegExp(r'[^\d]'), '');
    final buffer = StringBuffer();

    for (int i = 0; i < digits.length && i < 11; i++) {
      if (i == 3 || i == 6) buffer.write('.');
      if (i == 9) buffer.write('-');
      buffer.write(digits[i]);
    }

    final text = buffer.toString();
    return TextEditingValue(
      text: text,
      selection: TextSelection.collapsed(offset: text.length),
    );
  }
}

class _PhoneInputFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(
      TextEditingValue oldValue, TextEditingValue newValue) {
    final digits = newValue.text.replaceAll(RegExp(r'[^\d]'), '');
    final buffer = StringBuffer();

    for (int i = 0; i < digits.length && i < 11; i++) {
      if (i == 0) buffer.write('(');
      if (i == 2) buffer.write(') ');
      if (digits.length <= 10 && i == 6) buffer.write('-');
      if (digits.length == 11 && i == 7) buffer.write('-');
      buffer.write(digits[i]);
    }

    final text = buffer.toString();
    return TextEditingValue(
      text: text,
      selection: TextSelection.collapsed(offset: text.length),
    );
  }
}

class _CepInputFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(
      TextEditingValue oldValue, TextEditingValue newValue) {
    final digits = newValue.text.replaceAll(RegExp(r'[^\d]'), '');
    final buffer = StringBuffer();

    for (int i = 0; i < digits.length && i < 8; i++) {
      if (i == 5) buffer.write('-');
      buffer.write(digits[i]);
    }

    final text = buffer.toString();
    return TextEditingValue(
      text: text,
      selection: TextSelection.collapsed(offset: text.length),
    );
  }
}

class _DateInputFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(
      TextEditingValue oldValue, TextEditingValue newValue) {
    final digits = newValue.text.replaceAll(RegExp(r'[^\d]'), '');
    final buffer = StringBuffer();

    for (int i = 0; i < digits.length && i < 8; i++) {
      if (i == 2 || i == 4) buffer.write('/');
      buffer.write(digits[i]);
    }

    final text = buffer.toString();
    return TextEditingValue(
      text: text,
      selection: TextSelection.collapsed(offset: text.length),
    );
  }
}
