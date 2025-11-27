<?php
require_once __DIR__ . '/../Config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use CoffeeCode\Router\Router;
use Source\Boot\Session;

ob_start();

$session = new Session();
$route = new Router(url(), ":");
$route->namespace("Source\Controllers");

$route->group(null);
$route->get("/", "HomeController:index");
$route->get("/logout", "Login:logout");
$route->get("/cadastro", "HomeController:register");
$route->post("/cadastrar", "HomeController:register");
$route->get("/confirma", "HomeController:confirm");
$route->get("/obrigado/{email}", "HomeController:success");
$route->get("/recuperar", "HomeController:recover");
$route->post("/recuperar", "HomeController:recover");
$route->get("/recuperar/{email}/{token}", "HomeController:reset");
$route->post("/recuperar/resetar", "HomeController:reset");
$route->get("/telainicial", "Dash:index");
$route->post("/swap", "Dash:trocar_empresa");
$route->post("/swapdv", "Dash:swapEmpDev");

// equipamentos
$route->group("equipamentos");
$route->get("/", "EquipamentosController:index");
$route->get("/form", "EquipamentosController:form");
$route->get("/form/{id_equipamento}", "EquipamentosController:form");
$route->post("/excluir", "EquipamentosController:excluir");
$route->post("/salvar", "EquipamentosController:salvar");
$route->get("/gestaoeqp", "EquipamentosController:gestaoeqp");
$route->post("/salvar_local", "EquipamentosController:salvarLocal");
$route->post("/excluir_local", "EquipamentosController:excluirLocal");
$route->post("/salvar_mov", "EquipamentosController:salvarMov");
$route->post("/solicitar_mov", "EquipamentosController:solicitarMov");
$route->post("/cancelar_mov", "EquipamentosController:cancelarMov");
$route->post("/listar_alocados", "EquipamentosController:listarAlocados");
$route->post("/listar_kardex", "EquipamentosController:listarKardex");
$route->post("/verificar_estoque", "EquipamentosController:verificaEstoque");
$route->post("/refresh_local", "EquipamentosController:refreshLocal");
$route->get("/listar_solicitacoes", "EquipamentosController:listarSolicitacoes");
$route->post("/retorna_solicitacao", "EquipamentosController:retornaSolicitacao");

//checklist
$route->group("checklist");
$route->get("/", "ChecklistController:index");
$route->get("/grupos", "ChecklistController:grupos");
$route->post("/salvargrupo", "ChecklistController:salvargrupo");
$route->post("/excluirgrupo", "ChecklistController:excluirgrupo");
$route->get("/itens", "ChecklistController:itens");
$route->post("/salvaritem", "ChecklistController:salvaritem");
$route->post("/retornaitem", "ChecklistController:retornaitem");

//cadastros de empresa adm
$route->group("emp2");
$route->post("/", "Dash:emp2");
$route->get("/", "Emp2Controller:index");
$route->get("/form", "Emp2Controller:form");
$route->get("/form/{id_emp2}", "Emp2Controller:form");
$route->post("/excluir", "Emp2Controller:excluir");
$route->post("/salvar", "Emp2Controller:salvar");

//aba grupo de empresas
$route->group("emp1");
$route->get("/", "Emp1Controller:index");
$route->get("/form", "Emp1Controller:form");
$route->get("/form/{id_emp1}", "Emp1Controller:form");
$route->post("/salvar", "Emp1Controller:salvar");
$route->post("/excluir", "Emp1Controller:excluir");


/**
 * WEB ROUTES
 */
$route->group("login");
$route->get("/", "Login:login");
$route->post("/", "Login:login");

//dash
$route->group("dash");
$route->get("/", "Dash:dash");
$route->get("/graficos", "Dash:graficos");
$route->post("/graficos", "Dash:graficos");

//dash financeiro
$route->group("dash-financeiro");
$route->get("/", "Dash:dashFinanceiro");

//empresa
$route->group("emp");
$route->post("/", "Dash:emp2");
$route->get("/", "Dash:emp2");
$route->post("/verifica_padroes", "Emp2Controller:verificaPadroes");

//profile
$route->group("profile");
$route->post("/", "Dash:profile");
$route->get("/", "Dash:profile");

//atalho de cadastros (subdash)
$route->group("cadastros");
$route->get("/", "Dash:cadastros");

//timeline
$route->group("timeline");
$route->get("/", "TimelineController:index");

//agenda
$route->group("agenda");
$route->get("/", "AgendaController:index");
$route->get("/refresh", "AgendaController:refreshAgenda");
$route->post("/refresh", "AgendaController:refreshAgenda");

//contas a pagar
$route->group("contas");
$route->get("/", "FinanceiroController:index");
$route->post("/salvarpag", "FinanceiroController:salvarpag");
$route->post("/salvarrec", "FinanceiroController:salvarrec");
$route->post("/edtpag", "FinanceiroController:edtpag");
$route->post("/edtrec", "FinanceiroController:edtrec");
$route->post("/salvaredit", "FinanceiroController:salvaredit");
$route->post("/estorno", "FinanceiroController:estorno");
$route->post("/estornar", "FinanceiroController:estornar");
$route->post("/excluirtudo", "FinanceiroController:excluirtudo");
$route->post("/excluir", "FinanceiroController:excluir");
$route->post("/estornar_parcial", "FinanceiroController:estornarParcial");

//Baixas de contas
$route->group("baixar");
$route->get("/", "BaixarController:index");
$route->post("/", "BaixarController:index");
$route->post("/salvar", "BaixarController:salvar");
$route->post("/pdf", "BaixarController:baixasPdf");
$route->post("/busca", "BaixarController:buscaBaixas");
$route->post("/excluir", "BaixarController:excluir");

//entidades
$route->group("ent");
$route->get("/{entfilha}", "EntController:index");
$route->post("/salvar", "EntController:salvar");
$route->get("/form", "EntController:form");
$route->get("/form/{id_ent}", "EntController:form");
$route->post("/verificar", "EntController:verificar");
$route->post("/excluir", "EntController:excluir");
$route->post("/reativar", "EntController:reativar");

//plconta
$route->group("plconta");
$route->get("/", "PlcontaController:index");
$route->post("/salvar", "PlcontaController:salvar");
$route->get("/form", "PlcontaController:form");
$route->get("/form/{id_plconta}", "PlcontaController:form");
$route->post("/excluir", "PlcontaController:excluir");

//operacao
$route->group("operacao");
$route->get("/", "OperacaoController:index");
$route->post("/salvar", "OperacaoController:salvar");
$route->get("/form", "OperacaoController:form");
$route->get("/form/{id_operacao}", "OperacaoController:form");
$route->post("/excluir", "OperacaoController:excluir");

//turnos
$route->group("turno");
$route->get("/", "TurnoController:index");
$route->post("/salvar", "TurnoController:salvar");
$route->get("/form", "TurnoController:form");
$route->get("/form/{id_turno}", "TurnoController:form");
$route->post("/excluir", "TurnoController:excluir");

//usuários
$route->group("users");
$route->get("/", "UsersController:index");
$route->post("/salvar", "UsersController:salvar");
$route->get("/form", "UsersController:form");
$route->get("/form/{id_user}", "UsersController:form");
$route->post("/excluir", "UsersController:excluir");

//** OS **//

//ordens de serviço
$route->group("ordens");
$route->get("/", "OsController:index");
$route->post("/salvar", "OsController:salvar");
$route->post("/cancelar", "OsController:statusCancelar");
$route->get("/form", "OsController:form");
$route->get("/form/{id_ordens}", "OsController:form");
$route->post("/excluir", "OsController:excluir");
$route->get("/pdf/{id}", "OsController:pdf");
$route->get("/pdf/{id}/{emp}", "OsController:pdf");
$route->post("/gerarecorrencia", "OsController:gerarRecorrencias");
$route->post("/materiais", "OsController:materiais");
$route->post("/verificamateriais", "OsController:verificaMateriais");
$route->post("/excluir_material", "OsController:excluirMaterial");
$route->post("/equipamentos", "OsController:equipamentos");
$route->post("/verificaequipamentos", "OsController:verificaEquipamentos");
$route->post("/excluir_equipamento", "OsController:excluirEquipamento");
$route->post("/verificachecklist", "OsController:verificaChecklist");
$route->get("/checklist", "OsController:checklist");
$route->post("/salvarchecklist", "OsController:salvarChecklist");
$route->post("/checklistpdf", "OsController:checklistPdf");
$route->post("/statustarefa", "OsController:statusTarefa");
$route->post("/duplicar", "OsController:duplicar");
$route->post("/verifica_os", "OsController:verificaOs");
$route->post("/verifica_rec", "OsController:verificaRec");
$route->post("/estorna_os", "OsController:estornaOs");
$route->post("/retorna_itens", "OsController:retornaItens");
$route->post("/carregar_pagina", "OsController:carregarPagina");

//setor
$route->group("setor");
$route->get("/", "SetorController:index");
$route->post("/salvar", "SetorController:salvar");
$route->get("/form", "SetorController:form");
$route->get("/form/{id_setor}", "SetorController:form");
$route->post("/excluir", "SetorController:excluir");

//status
$route->group("status");
$route->get("/", "StatusController:index");
$route->post("/salvar", "StatusController:salvar");
$route->get("/form", "StatusController:form");
$route->get("/form/{id_status}", "StatusController:form");
$route->post("/excluir", "StatusController:excluir");

//custogeral
$route->group("custogeral");
$route->get("/", "CustoGeralController:index");
$route->post("/salvar", "CustoGeralController:salvar");
$route->get("/form", "CustoGeralController:form");
$route->get("/form/{id_custogeral}", "CustoGeralController:form");
$route->post("/excluir", "CustoGeralController:excluir");

//servico
$route->group("servico");
$route->get("/", "ServicoController:index");
$route->post("/salvar", "ServicoController:salvar");
$route->get("/form", "ServicoController:form");
$route->get("/form/{id_servico}", "ServicoController:form");
$route->post("/excluir", "ServicoController:excluir");
$route->post("/retorna_servicos", "ServicoController:retornaServicos");

//obras
$route->group("obras");
$route->get("/", "ObrasController:index");
$route->post("/salvar", "ObrasController:salvar");
$route->get("/form", "ObrasController:form");
$route->get("/form/{id_obras}", "ObrasController:form");
$route->post("/listar", "ObrasController:listar");
$route->post("/excluir", "ObrasController:excluir");

//medicao
$route->group("medicao");
$route->post("/", "MedicaoController:salvar");
$route->post("/excluir", "MedicaoController:excluir");
$route->post("/excluir/{id_medicao}", "MedicaoController:excluir");
$route->post("/atualiza", "MedicaoController:atualiza");
$route->post("/atualiza2", "MedicaoController:atualiza2");

//materiais
$route->group("materiais");
$route->get("/", "MateriaisController:index");
$route->post("/salvar", "MateriaisController:salvar");
$route->get("/form", "MateriaisController:form");
$route->get("/form/{id_materiais}", "MateriaisController:form");
$route->post("/excluir", "MateriaisController:excluir");

//observações
$route->group("obs");
$route->get("/", "ObsController:index");
$route->get("/form", "ObsController:form");
$route->get("/form/{id_obs}", "ObsController:form");
$route->post("/salvar", "ObsController:salvar");
$route->post("/excluir", "ObsController:excluir");

//recorrencias
$route->group("recorrencias");
$route->post("/verifica", "RecorrenciasController:verifica");

//tipos de os
$route->group("tipo");
$route->get("/", "TipoController:index");
$route->get("/form", "TipoController:form");
$route->get("/form/{id_tipo}", "TipoController:form");
$route->post("/salvar", "TipoController:salvar");
$route->post("/excluir", "TipoController:excluir");

//** OPERADOR MOBILE **//

//DASHBOARD
$route->group("oper_dash");
$route->get("/", "OperDash:dash");
$route->get("/oper", "OperDash:oper");
$route->post("/oper", "OperDash:oper");

//CALENDARIO
$route->group("oper_calendario");
$route->get("/", "OperCalendario:index");
$route->post("/", "OperCalendario:retornaDias");

//ORDENS
$route->group("oper_os1");
$route->get("/", "OperOs1:index");

//MOVIMENTAÇÃO
$route->group("oper_mov");
$route->get("/", "OperMov:index");
$route->post("/retorna_solicitacao", "OperMov:retornaSolicitacao");
$route->post("/verificar_estoque", "OperMov:verificarEstoque");
$route->post("/solicitar_mov", "OperMov:solicitarMov");
$route->post("/cancelar_mov", "OperMov:cancelarMov");

//TAREFAS
$route->group("oper_ordens");
$route->get("/", "OperOrdens:index");
$route->get("/{status}", "OperOrdens:index");
$route->post("/ordem", "OperOrdens:ordem");
$route->get("/ordem/{id}", "OperOrdens:ordem");
$route->post("/activity", "OperOrdens:activity");
$route->get("/pdf-os/{id}", "OperOrdens:gerarPdfOs");
$route->post("/pdf-os", "OperOrdens:sign");
$route->post("/obs", "OperOrdens:obs");
$route->post("/anexos", "OperOrdens:anexos");
$route->post("/deletearq", "OperOrdens:deleteos6");
$route->post("/mat", "OperOrdens:materiais");
$route->post("/deletemat", "OperOrdens:deleteos3");
$route->post("/aditivo", "OperOrdens:aditivo");

//** PONTO **//

//ponto
$route->group("ponto");
$route->get("/", "PontoController:index");
$route->get("/fechamento", "PontoController:fechamento");
$route->get("/folhas", "PontoController:folhas");
$route->get("/folhas/{mes}/{ano}", "PontoController:folhas");
$route->get("/editFolhas/{id_ponto1}", "PontoController:editFolhas");
$route->get("/pdf/{id_ponto1}", "PontoController:gerarPdf");
$route->post("/gerar", "PontoController:gerar");
$route->post("/salvar", "PontoController:salvar");
$route->post("/excluir", "PontoController:excluir");
$route->post("/filter", "PontoController:filter");
$route->post("/verificar", "PontoController:verificar");
$route->get("/feriados", "PontoController:feriados");
$route->post("/novo", "PontoController:novo");
$route->post("/excluirFeriado", "PontoController:excluirFeriado");

//faltas
$route->group("faltas");
$route->get("/", "FaltasController:lista");
$route->get("/obsForm", "FaltasController:faltasForm");
$route->get("/obsForm/{id_faltas}", "FaltasController:faltasForm");
$route->post("/apagar", "FaltasController:apagar");
$route->post("/cadastro", "FaltasController:salvar");

//arquivos
$route->group("files");
$route->get("/", "FilesController:index");
$route->get("/select", "FilesController:select");
$route->get("/emp", "FilesController:filesFormEmp");
$route->get("/func", "FilesController:filesFormFunc");
$route->get("/lista", "FilesController:lista");
$route->post("/salvar", "FilesController:salvar");
$route->post("/apagar", "FilesController:apagar");

//** RELATÓRIOS **/
$route->group("relatorios");
$route->get("/", "RelatoriosController:index");
$route->get("/ordens", "RelatoriosController:medicaoRel");
$route->post("/ordens", "RelatoriosController:medicaoRel");
$route->get("/pdf/{id}", "RelatoriosController:medicaoPdf");
$route->get("/pdfobra/{id}/{datai}/{dataf}", "RelatoriosController:obraPdfMedicao");
$route->get("/pdfobra/{id}", "RelatoriosController:obraPdfMedicao");
$route->get("/pdffunc/{id}/{datai}/{dataf}", "RelatoriosController:pdfFuncionario");
$route->get("/pdffunc/{id}", "RelatoriosController:pdfFuncionario");

$route->group("os2rel");
$route->get("/", "RelatoriosOs2Controller:index");
$route->post("/resultados", "RelatoriosOs2Controller:retornaRelatorio");
$route->post("/pdfos2", "RelatoriosOs2Controller:pdfOs2");
$route->get("/pdfos2b", "RelatoriosOs2Controller:pdfOs2b");

$route->group("servicosrel");
$route->get("/", "RelatoriosServicosController:index");
$route->post("/resultados", "RelatoriosServicosController:retornaRelatorio");
$route->post("/pdfservicos", "RelatoriosServicosController:pdfservicos");

$route->group("financeirorel");
$route->get("/", "RelatoriosFinanceiroController:index");
$route->get("/pagar", "RelatoriosFinanceiroController:pagar");
$route->get("/receber", "RelatoriosFinanceiroController:receber");
$route->post("/resultados", "RelatoriosFinanceiroController:retornaRelatorio");
$route->post("/pdf", "RelatoriosFinanceiroController:pdf");

$route->group("logs");
$route->get("/", "LogController:index");
$route->post("/pesqLogs", "LogController:pesquisaLogs");
$route->post("/", "LogController:selectUsers");
$route->post("/acao", "LogController:logAcao");
$route->get("/exp", "ExpiracaoController:enviarAvisos");

$route->group('fila');
$route->get("/", "FilaController:index");
$route->get("/form", "FilaController:form");
$route->get("/form/{id_fila}", "FilaController:form");
$route->post("/salvar", "FilaController:salvar");
$route->post("/excluir", "FilaController:excluir");

$route->group('tipolocal');
$route->get("/", "LocalTipoController:index");
$route->get("/form", "LocalTipoController:form");
$route->get("/form/{id_tipolocal}", "LocalTipoController:form");
$route->post("/salvar", "LocalTipoController:salvar");
$route->post("/excluir", "LocalTipoController:excluir");

$route->group('local');
$route->get("/", "LocalController:index");
$route->get("/form", "LocalController:form");
$route->get("/form/{id_local}", "LocalController:form");
$route->post("/salvar", "LocalController:salvar");
$route->post("/excluir", "LocalController:excluir");

$route->group('atendimento');
$route->get("/", "AtendimentoController:index");
$route->post("/iniciaAtendimento", "AtendimentoController:iniciaAtendimento");
$route->get("/atendente", "AtendimentoController:atendente");
$route->post("/iniciar", "AtendimentoController:iniciar");
$route->post("/pausar", "AtendimentoController:pausar");
$route->post("/encerrar", "AtendimentoController:encerrar");

$route->group("senhas");
$route->get("/gerar", "FilaController:gerarSenha");
$route->get("/chamar", "FilaController:chamarSenha");
$route->post("/tipo", "FilaController:tipo");

$route->group('cep');
$route->post("/", "CepCacheController:buscaPorCep");






//** TEMPORÁRIOS **//
//$route->group("temp");
//$route->get("/", "TempController:index");
//$route->post("/atualizaRegistros", "TempController:atualizaRegistros");


/**
 * ERROS
 */
$route->group("ops");
$route->get("/{errcode}", "Web:error");

$route->dispatch();

if ($route->error()) {
    // Evita loop de redirecionamento em requisições AJAX
    if (
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        http_response_code($route->error());
        echo json_encode(['erro' => 'Rota não encontrada ou falha interna.', 'codigo' => $route->error()]);
    } else {
        $route->redirect("/ops/{$route->error()}");
    }
}


ob_end_flush();
