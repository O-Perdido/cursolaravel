import './bootstrap';
import Inputmask from 'inputmask';
import './navigation';


document.addEventListener('DOMContentLoaded', function () {

    // Configuração para valores monetários
    var valor_decimal = new Inputmask("currency", {
        radixPoint: ",",
        groupSeparator: ".",
        digits: 2,
        autoGroup: true,
        prefix: "",
        rightAlign: false,
        numericInput: true, // Permite digitação da direita para a esquerda
        placeholder: "0"
    });

    // Aplicar a máscara nos campos de cadastro de termo
    const bolsaFixo = document.querySelector("#valor_bolsa_fixo");
    if (bolsaFixo) valor_decimal.mask(bolsaFixo);

    const transporteFixo = document.querySelector("#auxilio_transporte_fixo");
    if (transporteFixo) valor_decimal.mask(transporteFixo);

    // Aplicar a máscara nos campos de alteração de termo
    const bolsaAlteracao = document.querySelector("#valor_bolsa_alteracao");
    if (bolsaAlteracao) valor_decimal.mask(bolsaAlteracao);

    const transporteAlteracao = document.querySelector("#auxilio_transporte_alteracao");
    if (transporteAlteracao) valor_decimal.mask(transporteAlteracao);

    // Aplicar a máscara nos campos de descontos 
    const descontoFolha = document.querySelector(".descontos_folha");
    if (descontoFolha) valor_decimal.mask(descontoFolha);

    // Configuração para CPF
    var cpfMask = new Inputmask({
        mask: "999.999.999-99", // Formato do CPF
        placeholder: "", // Caractere de preenchimento
        showMaskOnHover: false, // Não exibir a máscara ao passar o mouse
        showMaskOnFocus: false, // Exibir a máscara ao focar no campo
        rightAlign: false, // Alinhar à esquerda
        clearIncomplete: true // Limpar o campo se o CPF estiver incompleto
    });

    // Aplicar a máscara nos campos de CPF
    const cpfFields = document.querySelectorAll("#numero_cpf, #cpf_supervisor, #cpf, #cpf_representante, #cpf_recuperacao");
    cpfFields.forEach(field => {
        if (field) cpfMask.mask(field);
    });

    // Configuração para CNPJ
    var cnpjMask = new Inputmask({
        mask: "99.999.999/9999-99", // Formato do CNPJ
        placeholder: "", // Caractere de preenchimento
        showMaskOnHover: false, // Não exibir a máscara ao passar o mouse
        showMaskOnFocus: false, // Exibir a máscara ao focar no campo
        rightAlign: false, // Alinhar à esquerda
        clearIncomplete: true // Limpar o campo se o CNPJ estiver incompleto
    });

    // Aplicar a máscara nos campos de CNPJ
    const cnpjFields = document.querySelectorAll("#numero_cnpj, #cnpj");
    cnpjFields.forEach(field => {
        if (field) cnpjMask.mask(field);
    });

    // Configuração para Telefone
    var telefoneMask = new Inputmask({
        mask: "(99) 9999-9999", // Formato do telefone fixo
        placeholder: "", // Caractere de preenchimento
        showMaskOnHover: false, // Não exibir a máscara ao passar o mouse
        showMaskOnFocus: false, // Exibir a máscara ao focar no campo
        rightAlign: false, // Alinhar à esquerda
        clearIncomplete: true // Limpar o campo se o telefone estiver incompleto
    });

    // Aplicar a máscara nos campos de Telefone
    const telefoneFields = document.querySelectorAll("#numero_telefone");
    telefoneFields.forEach(field => {
        if (field) telefoneMask.mask(field);
    });

    // Configuração para Celular
    var celularMask = new Inputmask({
        mask: "(99) 99999-9999", // Formato do celular
        placeholder: "", // Caractere de preenchimento
        showMaskOnHover: false, // Não exibir a máscara ao passar o mouse
        showMaskOnFocus: false, // Exibir a máscara ao focar no campo
        rightAlign: false, // Alinhar à esquerda
        clearIncomplete: true // Limpar o campo se o celular estiver incompleto
    });

    // Aplicar a máscara nos campos de Celular
    const celularFields = document.querySelectorAll("#numero_celular");
    celularFields.forEach(field => {
        if (field) celularMask.mask(field);
    });

    // Configuração para CEP
    var cepMask = new Inputmask({
        mask: "99999-999", // Formato do CEP
        placeholder: "", // Caractere de preenchimento
        showMaskOnHover: false, // Não exibir a máscara ao passar o mouse
        showMaskOnFocus: false, // Exibir a máscara ao focar no campo
        rightAlign: false, // Alinhar à esquerda
        clearIncomplete: true // Limpar o campo se o CEP estiver incompleto
    });

    // Aplicar a máscara nos campos de CEP
    const cepFields = document.querySelectorAll("#numero_cep");
    cepFields.forEach(field => {
        if (field) cepMask.mask(field);
    });
});