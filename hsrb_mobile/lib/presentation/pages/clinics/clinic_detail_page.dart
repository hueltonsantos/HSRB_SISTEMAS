import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/constants/app_strings.dart';
import '../../../core/services/clinic_service.dart';
import '../../../data/models/clinic_model.dart';
import 'clinic_form_page.dart';

class ClinicDetailPage extends StatefulWidget {
  final int clinicId;

  const ClinicDetailPage({super.key, required this.clinicId});

  @override
  State<ClinicDetailPage> createState() => _ClinicDetailPageState();
}

class _ClinicDetailPageState extends State<ClinicDetailPage> {
  ClinicModel? _clinic;
  bool _isLoading = true;
  String? _error;
  bool _changed = false;

  @override
  void initState() {
    super.initState();
    _loadClinic();
  }

  Future<void> _loadClinic() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final service = context.read<ClinicService>();
      final clinic = await service.getClinic(widget.clinicId);
      setState(() {
        _clinic = clinic;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString().replaceAll('Exception: ', '');
        _isLoading = false;
      });
    }
  }

  Future<void> _navigateToEdit() async {
    final result = await Navigator.push<bool>(
      context,
      MaterialPageRoute(
        builder: (_) => RepositoryProvider.value(
          value: context.read<ClinicService>(),
          child: ClinicFormPage(clinic: _clinic),
        ),
      ),
    );

    if (result == true) {
      _changed = true;
      _loadClinic();
    }
  }

  Future<void> _confirmDelete() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text(AppStrings.delete),
        content: Text('Deseja realmente excluir a clinica "${_clinic?.nome}"?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text(AppStrings.cancel),
          ),
          TextButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: TextButton.styleFrom(foregroundColor: AppColors.danger),
            child: const Text(AppStrings.delete),
          ),
        ],
      ),
    );

    if (confirmed == true && mounted) {
      try {
        final service = context.read<ClinicService>();
        await service.deleteClinic(widget.clinicId);
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text(AppStrings.deleteSuccess),
              backgroundColor: AppColors.success,
            ),
          );
          Navigator.pop(context, true);
        }
      } catch (e) {
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
  }

  String _formatDateTime(String dateTime) {
    try {
      final parts = dateTime.split(' ');
      final dateParts = parts[0].split('-');
      if (dateParts.length == 3) {
        final time = parts.length > 1 ? ' ${parts[1].substring(0, 5)}' : '';
        return '${dateParts[2]}/${dateParts[1]}/${dateParts[0]}$time';
      }
    } catch (_) {}
    return dateTime;
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: true,
      onPopInvokedWithResult: (didPop, result) {},
      child: Scaffold(
        appBar: AppBar(
          title: const Text('Detalhes da Clinica'),
          actions: [
            if (_clinic != null) ...[
              IconButton(
                icon: const Icon(Icons.edit),
                onPressed: _navigateToEdit,
              ),
              IconButton(
                icon: const Icon(Icons.delete, color: AppColors.danger),
                onPressed: _confirmDelete,
              ),
            ],
          ],
        ),
        body: RefreshIndicator(
          onRefresh: _loadClinic,
          child: _buildBody(),
        ),
      ),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_error != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline, size: 64, color: AppColors.danger),
              const SizedBox(height: 16),
              Text(
                _error!,
                textAlign: TextAlign.center,
                style: const TextStyle(color: AppColors.textSecondary),
              ),
              const SizedBox(height: 16),
              ElevatedButton.icon(
                onPressed: _loadClinic,
                icon: const Icon(Icons.refresh),
                label: const Text(AppStrings.tryAgain),
              ),
            ],
          ),
        ),
      );
    }

    final clinic = _clinic!;

    return SingleChildScrollView(
      physics: const AlwaysScrollableScrollPhysics(),
      padding: const EdgeInsets.all(16),
      child: Column(
        children: [
          _buildHeaderCard(clinic),
          const SizedBox(height: 16),
          _buildSection(
            title: 'Dados da Clinica',
            icon: Icons.business,
            children: [
              _buildInfoRow('Nome Fantasia', clinic.nome),
              if (clinic.razaoSocial != null && clinic.razaoSocial!.isNotEmpty)
                _buildInfoRow('Razao Social', clinic.razaoSocial!),
              if (clinic.cnpj != null && clinic.cnpj!.isNotEmpty)
                _buildInfoRow('CNPJ', clinic.cnpj!),
              if (clinic.responsavel != null && clinic.responsavel!.isNotEmpty)
                _buildInfoRow('Responsavel', clinic.responsavel!),
            ],
          ),
          const SizedBox(height: 16),
          _buildSection(
            title: 'Contato',
            icon: Icons.contact_phone,
            children: [
              _buildInfoRow(AppStrings.phone, clinic.telefone),
              if (clinic.celular != null && clinic.celular!.isNotEmpty)
                _buildInfoRow('Celular', clinic.celular!),
              if (clinic.email != null && clinic.email!.isNotEmpty)
                _buildInfoRow(AppStrings.email, clinic.email!),
              if (clinic.site != null && clinic.site!.isNotEmpty)
                _buildInfoRow('Site', clinic.site!),
            ],
          ),
          const SizedBox(height: 16),
          _buildSection(
            title: AppStrings.address,
            icon: Icons.location_on,
            children: [
              if (clinic.endereco.isNotEmpty)
                _buildInfoRow('Logradouro', '${clinic.endereco}${clinic.numero != null ? ', ${clinic.numero}' : ''}'),
              if (clinic.complemento != null && clinic.complemento!.isNotEmpty)
                _buildInfoRow('Complemento', clinic.complemento!),
              if (clinic.bairro != null && clinic.bairro!.isNotEmpty)
                _buildInfoRow('Bairro', clinic.bairro!),
              if (clinic.cidade.isNotEmpty)
                _buildInfoRow(AppStrings.city, '${clinic.cidade} - ${clinic.estado}'),
              if (clinic.cep != null && clinic.cep!.isNotEmpty)
                _buildInfoRow(AppStrings.zipCode, clinic.cep!),
            ],
          ),
          if (clinic.especialidades != null && clinic.especialidades!.isNotEmpty) ...[
            const SizedBox(height: 16),
            _buildSection(
              title: AppStrings.specialties,
              icon: Icons.medical_services,
              children: clinic.especialidades!.map((esp) {
                return Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                  child: Row(
                    children: [
                      const Icon(Icons.check_circle, size: 16, color: AppColors.success),
                      const SizedBox(width: 8),
                      Text(
                        esp.nome,
                        style: const TextStyle(fontSize: 14),
                      ),
                    ],
                  ),
                );
              }).toList(),
            ),
          ],
          const SizedBox(height: 16),
          _buildSection(
            title: 'Informacoes do Sistema',
            icon: Icons.info_outline,
            children: [
              _buildInfoRow('Status', clinic.isActive ? AppStrings.active : AppStrings.inactive),
              if (clinic.dataCadastro != null)
                _buildInfoRow('Cadastrado em', _formatDateTime(clinic.dataCadastro!)),
              if (clinic.ultimaAtualizacao != null)
                _buildInfoRow('Atualizado em', _formatDateTime(clinic.ultimaAtualizacao!)),
            ],
          ),
          const SizedBox(height: 32),
        ],
      ),
    );
  }

  Widget _buildHeaderCard(ClinicModel clinic) {
    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        gradient: AppColors.primaryGradient,
        borderRadius: BorderRadius.circular(16),
        boxShadow: AppColors.defaultShadow,
      ),
      padding: const EdgeInsets.all(20),
      child: Column(
        children: [
          CircleAvatar(
            radius: 40,
            backgroundColor: Colors.white,
            child: Text(
              clinic.nome.isNotEmpty ? clinic.nome[0].toUpperCase() : '?',
              style: const TextStyle(
                fontSize: 36,
                fontWeight: FontWeight.w700,
                color: AppColors.primary,
              ),
            ),
          ),
          const SizedBox(height: 12),
          Text(
            clinic.nome,
            style: const TextStyle(
              color: Colors.white,
              fontSize: 22,
              fontWeight: FontWeight.w700,
            ),
            textAlign: TextAlign.center,
          ),
          if (clinic.cidade.isNotEmpty) ...[
            const SizedBox(height: 4),
            Text(
              '${clinic.cidade} - ${clinic.estado}',
              style: TextStyle(
                color: Colors.white.withOpacity(0.9),
                fontSize: 14,
              ),
            ),
          ],
          const SizedBox(height: 8),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
            decoration: BoxDecoration(
              color: clinic.isActive
                  ? Colors.white.withOpacity(0.2)
                  : Colors.red.withOpacity(0.3),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Text(
              clinic.isActive ? AppStrings.active : AppStrings.inactive,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 12,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSection({
    required String title,
    required IconData icon,
    required List<Widget> children,
  }) {
    return Card(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
            child: Row(
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
            ),
          ),
          const Divider(height: 1),
          ...children,
          const SizedBox(height: 8),
        ],
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 130,
            child: Text(
              label,
              style: const TextStyle(
                color: AppColors.textSecondary,
                fontSize: 13,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(
                color: AppColors.textPrimary,
                fontSize: 14,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
