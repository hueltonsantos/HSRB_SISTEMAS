import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../bloc/price_bloc.dart';
import '../../../data/repositories/price_repository.dart';
import '../../../data/repositories/specialty_repository.dart';
import '../../../core/services/auth_service.dart';

/// Página de Tabela de Preços
class PricesListPage extends StatelessWidget {
  const PricesListPage({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => PriceBloc(
        repository: PriceRepository(authService: context.read<AuthService>()),
      )..add(LoadPrices()),
      child: const PricesListView(),
    );
  }
}

class PricesListView extends StatefulWidget {
  const PricesListView({Key? key}) : super(key: key);

  @override
  State<PricesListView> createState() => _PricesListViewState();
}

class _PricesListViewState extends State<PricesListView> {
  int? _selectedEspecialidadeId;
  String? _selectedEspecialidadeNome;
  final TextEditingController _searchController = TextEditingController();

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _onSearch(String value) {
    context.read<PriceBloc>().add(LoadPrices(
      search: value,
      especialidadeId: _selectedEspecialidadeId,
    ));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Tabela de Preços'),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: _showFilterDialog,
          ),
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(60),
          child: Padding(
            padding: const EdgeInsets.all(8.0),
            child: TextField(
              controller: _searchController,
              decoration: InputDecoration(
                hintText: 'Buscar procedimento...',
                prefixIcon: const Icon(Icons.search),
                filled: true,
                fillColor: Colors.white,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8),
                  borderSide: BorderSide.none,
                ),
                contentPadding: const EdgeInsets.symmetric(vertical: 0),
              ),
              onSubmitted: _onSearch,
            ),
          ),
        ),
      ),
      body: BlocConsumer<PriceBloc, PriceState>(
        listener: (context, state) {
          if (state is PriceOperationSuccess) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text(state.message), backgroundColor: Colors.green),
            );
            context.read<PriceBloc>().add(LoadPrices(
              search: _searchController.text,
              especialidadeId: _selectedEspecialidadeId,
            ));
          } else if (state is PriceError) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text(state.message), backgroundColor: Colors.red),
            );
          }
        },
        builder: (context, state) {
          return Column(
            children: [
              if (_selectedEspecialidadeId != null)
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  color: Colors.blue.shade50,
                  child: Row(
                    children: [
                      const Icon(Icons.filter_alt, size: 16),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          'Filtro: ${_selectedEspecialidadeNome ?? "Especialidade"}',
                          style: const TextStyle(fontWeight: FontWeight.bold),
                        ),
                      ),
                      TextButton(
                        onPressed: () {
                          setState(() {
                            _selectedEspecialidadeId = null;
                            _selectedEspecialidadeNome = null;
                          });
                          context.read<PriceBloc>().add(LoadPrices(
                            search: _searchController.text,
                          ));
                        },
                        child: const Text('Limpar'),
                      ),
                    ],
                  ),
                ),
              Expanded(
                child: _buildList(state),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildList(PriceState state) {
    if (state is PriceLoading) {
      return const Center(child: CircularProgressIndicator());
    } else if (state is PricesLoaded) {
      final prices = state.prices;

      if (prices.isEmpty) {
        return const Center(child: Text('Nenhum preço encontrado'));
      }

      return ListView.builder(
        itemCount: prices.length,
        padding: const EdgeInsets.all(16),
        itemBuilder: (context, index) {
          final price = prices[index];
          // Determine colors based on margins (optional visual cue)
          // final repasse = double.tryParse(price['valor_repasse']?.toString() ?? '0') ?? 0;
          // final valor = double.tryParse(price['valor_paciente']?.toString() ?? '0') ?? 0;
          
          return Card(
            margin: const EdgeInsets.only(bottom: 12),
            child: ListTile(
              title: Text(
                price['procedimento'] ?? 'Sem nome',
                style: const TextStyle(fontWeight: FontWeight.bold),
              ),
              subtitle: Text(price['especialidade_nome'] ?? 'Geral'),
              trailing: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text(
                    'R\$ ${price['valor_paciente']}',
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                      color: Colors.green,
                    ),
                  ),
                  Text(
                    'Repasse: R\$ ${price['valor_repasse']}',
                    style: const TextStyle(fontSize: 12, color: Colors.grey),
                  ),
                ],
              ),
              onTap: () => _showEditDialog(price),
            ),
          );
        },
      );
    }
    return const Center(child: Text('Tabela de Preços'));
  }

  void _showFilterDialog() async {
    // Show loading or fetch directly inside dialog with FutureBuilder
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Filtrar por Especialidade'),
        content: SizedBox(
          width: double.maxFinite,
          child: FutureBuilder<List<dynamic>>(
            // FIX: Using context.read from parent context (PricesListPage context) which has providers
            future: SpecialtyRepository(authService: context.read<AuthService>()).listSpecialties(),
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return const Center(child: CircularProgressIndicator());
              } else if (snapshot.hasError) {
                return Text('Erro: ${snapshot.error}');
              } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
                return const Text('Nenhuma especialidade encontrada');
              }

              final specialties = snapshot.data!;
              return ListView.builder(
                shrinkWrap: true,
                itemCount: specialties.length + 1,
                itemBuilder: (context, index) {
                  if (index == 0) {
                    return ListTile(
                      title: const Text('Todas'),
                      onTap: () {
                        setState(() {
                          _selectedEspecialidadeId = null;
                          _selectedEspecialidadeNome = null;
                        });
                        context.read<PriceBloc>().add(LoadPrices(
                          search: _searchController.text,
                        ));
                        Navigator.pop(ctx);
                      },
                    );
                  }
                  final specialty = specialties[index - 1];
                  return ListTile(
                    title: Text(specialty['nome']),
                    onTap: () {
                      setState(() {
                        _selectedEspecialidadeId = specialty['id'];
                        _selectedEspecialidadeNome = specialty['nome'];
                      });
                      context.read<PriceBloc>().add(LoadPrices(
                        search: _searchController.text,
                        especialidadeId: specialty['id'],
                      ));
                      Navigator.pop(ctx);
                    },
                  );
                },
              );
            },
          ),
        ),
      ),
    );
  }

  void _showEditDialog(Map<String, dynamic> price) {
    final valorPacienteController = TextEditingController(
      text: price['valor_paciente'].toString(),
    );
    final valorRepasseController = TextEditingController(
      text: price['valor_repasse'].toString(),
    );
    final formKey = GlobalKey<FormState>();

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text('Editar: ${price['procedimento']}'),
        content: Form(
          key: formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextFormField(
                controller: valorPacienteController,
                decoration: const InputDecoration(
                  labelText: 'Valor Paciente (R\$)',
                  border: OutlineInputBorder(),
                ),
                keyboardType: const TextInputType.numberWithOptions(decimal: true),
                validator: (value) {
                  if (value == null || value.isEmpty) return 'Obrigatório';
                  if (double.tryParse(value.replaceAll(',', '.')) == null) return 'Inválido';
                  return null;
                },
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: valorRepasseController,
                decoration: const InputDecoration(
                  labelText: 'Valor Repasse (R\$)',
                  border: OutlineInputBorder(),
                ),
                keyboardType: const TextInputType.numberWithOptions(decimal: true),
                validator: (value) {
                   if (value == null || value.isEmpty) return 'Obrigatório';
                   if (double.tryParse(value.replaceAll(',', '.')) == null) return 'Inválido';
                   return null;
                },
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Cancelar'),
          ),
          ElevatedButton(
            onPressed: () {
              if (formKey.currentState!.validate()) {
                final valorPaciente = double.parse(valorPacienteController.text.replaceAll(',', '.'));
                final valorRepasse = double.parse(valorRepasseController.text.replaceAll(',', '.'));
                
                context.read<PriceBloc>().add(UpdatePrice(
                  id: price['id'],
                  valorPaciente: valorPaciente,
                  valorRepasse: valorRepasse,
                ));
                Navigator.pop(ctx);
              }
            },
            child: const Text('Salvar'),
          ),
        ],
      ),
    );
  }
}
