import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/constants/app_strings.dart';
import '../../../core/services/guide_service.dart';
import '../../../core/services/patient_service.dart';
import '../../../core/services/specialty_service.dart';
import '../../../data/models/patient_model.dart';
import '../../../data/models/specialty_model.dart';

class GuideFormPage extends StatefulWidget {
  const GuideFormPage({super.key});

  @override
  State<GuideFormPage> createState() => _GuideFormPageState();
}

class _GuideFormPageState extends State<GuideFormPage> {
  final _formKey = GlobalKey<FormState>();
  bool _isSaving = false;
  bool _isLoadingData = true;

  List<PatientModel> _patients = [];
  List<SpecialtyModel> _specialties = [];
  List<ProcedureModel> _procedures = [];

  int? _selectedPacienteId;
  int? _selectedEspecialidadeId;
  int? _selectedProcedimentoId;

  late final TextEditingController _dataController;
  late final TextEditingController _horarioController;
  late final TextEditingController _observacoesController;

  @override
  void initState() {
    super.initState();
    _dataController = TextEditingController();
    _horarioController = TextEditingController();
    _observacoesController = TextEditingController();
    _loadFormData();
  }

  @override
  void dispose() {
    _dataController.dispose();
    _horarioController.dispose();
    _observacoesController.dispose();
    super.dispose();
  }

  Future<void> _loadFormData() async {
    try {
      final patientService = context.read<PatientService>();
      final specialtyService = context.read<SpecialtyService>();

      final patientsResult = await patientService.listPatients(page: 1, limit: 100);
      final specialtiesResult = await specialtyService.listSpecialties(page: 1, limit: 100);

      setState(() {
        _patients = patientsResult.items;
        _specialties = specialtiesResult.items;
        _isLoadingData = false;
      });
    } catch (e) {
      setState(() => _isLoadingData = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Erro ao carregar dados: ${e.toString().replaceAll('Exception: ', '')}'),
            backgroundColor: AppColors.danger,
          ),
        );
      }
    }
  }

  void _onSpecialtyChanged(int? specialtyId) {
    setState(() {
      _selectedEspecialidadeId = specialtyId;
      _selectedProcedimentoId = null;
      _procedures = [];
    });

    if (specialtyId != null) {
      final specialty = _specialties.firstWhere((s) => s.id == specialtyId);
      if (specialty.procedimentos != null) {
        setState(() {
          _procedures = specialty.procedimentos!.where((p) => p.isActive).toList();
        });
      }
    }
  }

  Future<void> _selectDate() async {
    final now = DateTime.now();
    final picked = await showDatePicker(
      context: context,
      initialDate: now,
      firstDate: now,
      lastDate: DateTime(now.year + 1, 12, 31),
      locale: const Locale('pt', 'BR'),
    );

    if (picked != null) {
      _dataController.text =
          '${picked.day.toString().padLeft(2, '0')}/${picked.month.toString().padLeft(2, '0')}/${picked.year}';
    }
  }

  Future<void> _selectTime() async {
    final picked = await showTimePicker(
      context: context,
      initialTime: TimeOfDay.now(),
    );

    if (picked != null) {
      _horarioController.text =
          '${picked.hour.toString().padLeft(2, '0')}:${picked.minute.toString().padLeft(2, '0')}';
    }
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

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSaving = true);

    try {
      final service = context.read<GuideService>();

      final data = <String, dynamic>{
        'paciente_id': _selectedPacienteId,
        'procedimento_id': _selectedProcedimentoId,
        'data_agendamento': _formatDateForApi(_dataController.text),
      };

      if (_horarioController.text.trim().isNotEmpty) {
        data['horario_agendamento'] = _horarioController.text.trim();
      }
      if (_observacoesController.text.trim().isNotEmpty) {
        data['observacoes'] = _observacoesController.text.trim();
      }

      await service.createGuide(data);

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text(AppStrings.saveSuccess),
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
        title: const Text('Nova Guia de Encaminhamento'),
      ),
      body: _isLoadingData
          ? const Center(child: CircularProgressIndicator())
          : Form(
              key: _formKey,
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    _buildSectionHeader('Paciente', Icons.person),
                    const SizedBox(height: 12),
                    DropdownButtonFormField<int>(
                      value: _selectedPacienteId,
                      decoration: InputDecoration(
                        labelText: 'Selecione o Paciente *',
                        prefixIcon: const Icon(Icons.person),
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
                      items: _patients
                          .map((p) => DropdownMenuItem(value: p.id, child: Text(p.nome)))
                          .toList(),
                      onChanged: (v) => setState(() => _selectedPacienteId = v),
                      validator: (v) => v == null ? AppStrings.requiredField : null,
                    ),
                    const SizedBox(height: 24),

                    _buildSectionHeader('Procedimento', Icons.medical_services),
                    const SizedBox(height: 12),
                    DropdownButtonFormField<int>(
                      value: _selectedEspecialidadeId,
                      decoration: InputDecoration(
                        labelText: 'Especialidade *',
                        prefixIcon: const Icon(Icons.local_hospital),
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
                      items: _specialties
                          .where((s) => s.isActive)
                          .map((s) => DropdownMenuItem(value: s.id, child: Text(s.nome)))
                          .toList(),
                      onChanged: _onSpecialtyChanged,
                      validator: (v) => v == null ? AppStrings.requiredField : null,
                    ),
                    const SizedBox(height: 12),
                    DropdownButtonFormField<int>(
                      value: _selectedProcedimentoId,
                      decoration: InputDecoration(
                        labelText: 'Procedimento *',
                        prefixIcon: const Icon(Icons.list_alt),
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
                      items: _procedures
                          .map((p) => DropdownMenuItem(value: p.id, child: Text(p.procedimento)))
                          .toList(),
                      onChanged: (v) => setState(() => _selectedProcedimentoId = v),
                      validator: (v) => v == null ? AppStrings.requiredField : null,
                    ),
                    const SizedBox(height: 24),

                    _buildSectionHeader('Agendamento', Icons.calendar_today),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Expanded(
                          child: TextFormField(
                            controller: _dataController,
                            decoration: InputDecoration(
                              labelText: 'Data *',
                              prefixIcon: const Icon(Icons.calendar_today),
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
                            readOnly: true,
                            onTap: _selectDate,
                            validator: (v) {
                              if (v == null || v.trim().isEmpty) return AppStrings.requiredField;
                              return null;
                            },
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: TextFormField(
                            controller: _horarioController,
                            decoration: InputDecoration(
                              labelText: 'Horario',
                              prefixIcon: const Icon(Icons.access_time),
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
                            readOnly: true,
                            onTap: _selectTime,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 24),

                    _buildSectionHeader(AppStrings.observations, Icons.notes),
                    const SizedBox(height: 12),
                    TextFormField(
                      controller: _observacoesController,
                      decoration: InputDecoration(
                        labelText: AppStrings.observations,
                        prefixIcon: const Icon(Icons.notes),
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
                      maxLines: 3,
                      textCapitalization: TextCapitalization.sentences,
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
                            : const Text(
                                'Gerar Guia',
                                style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
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
}
