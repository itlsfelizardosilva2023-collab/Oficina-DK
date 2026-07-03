<?php

include_once("Admistrador/configuracao/conexao.php");

$mensagens = [];
$erros = [];

if (isset($_GET['executar'])) {

    $sqls = [

        "Tabela clientes" => "
        CREATE TABLE IF NOT EXISTS clientes (
            id_cliente INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            telefone VARCHAR(20),
            numero_bi VARCHAR(20) NOT NULL,
            endereco VARCHAR(200),
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",

        "Tabela marcas" => "
        CREATE TABLE IF NOT EXISTS marcas (
            id_marca INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(50) NOT NULL UNIQUE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",

        "Tabela modelos" => "
        CREATE TABLE IF NOT EXISTS modelos (
            id_modelo INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(50) NOT NULL,
            id_marca INT NOT NULL,
            FOREIGN KEY (id_marca) REFERENCES marcas(id_marca)
            ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",

        "Tabela carros" => "
        CREATE TABLE IF NOT EXISTS carros (
            id_carro INT AUTO_INCREMENT PRIMARY KEY,
            id_modelo INT NOT NULL,
            matricula VARCHAR(20) UNIQUE,
            cor VARCHAR(30),
            id_cliente INT NOT NULL,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_modelo) REFERENCES modelos(id_modelo)
            ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
            ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",

        "Tabela estoque" => "
        CREATE TABLE IF NOT EXISTS estoque (
            id_estoque INT AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(50) UNIQUE,
            tipo ENUM('Material','Peça') NOT NULL,
            nome VARCHAR(100) NOT NULL,
            marca VARCHAR(50),
            quantidade INT NOT NULL DEFAULT 0,
            data_registo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_expiracao DATE,
            preco DECIMAL(10,2) DEFAULT 0.00
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",

        "Tabela funcionarios" => "
        CREATE TABLE IF NOT EXISTS funcionarios (
            id_funcionario INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL,
            endereco VARCHAR(230),
            telefone VARCHAR(20),
            cargo ENUM('mecanico','administrador','ajudante') NOT NULL,
            setor ENUM('mecanica','funilaria') NOT NULL,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('Activo','Inactivo','Ferias','Suspenso') DEFAULT 'Activo'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",

        "Tabela usuarios" => "
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_funcionario INT NOT NULL,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            senha VARCHAR(255) NOT NULL,
            nivel ENUM('admin','usuario') NOT NULL,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_master_admin TINYINT(1) DEFAULT 0,
            status ENUM('ativo','desativo') NOT NULL DEFAULT 'ativo',
            ativo TINYINT(1) DEFAULT 1,
            FOREIGN KEY (id_funcionario)
            REFERENCES funcionarios(id_funcionario)
            ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",

        "Tabela servicos" => "
        CREATE TABLE IF NOT EXISTS servicos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            placa_carro VARCHAR(50),
            nome_cliente VARCHAR(100),
            modelo_carro VARCHAR(100),
            endereco TEXT,
            obs TEXT,
            total DECIMAL(10,2),
            data_registo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            id_mecanico INT NULL,
            data DATETIME DEFAULT CURRENT_TIMESTAMP,
            status ENUM('concluido','pendente','andamento','cancelado') NOT NULL DEFAULT 'pendente'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",

        "Tabela servico_itens" => "
        CREATE TABLE IF NOT EXISTS servico_itens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_servico INT,
            codigo_da_peca VARCHAR(50),
            peca_materia VARCHAR(100),
            quantidade INT,
            preco DECIMAL(10,2),
            FOREIGN KEY (id_servico) REFERENCES servicos(id)
            ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",

        "Tabela contratos" => "
        CREATE TABLE IF NOT EXISTS contratos (
            id_contrato INT AUTO_INCREMENT PRIMARY KEY,
            id_cliente INT NOT NULL,
            numero_bi VARCHAR(50) DEFAULT NULL,
            preco_viatura DECIMAL(10,2) DEFAULT 0.00,
            tipo ENUM('VIP','Normal') DEFAULT NULL,
            data_inicio DATE DEFAULT NULL,
            data_fim DATE DEFAULT NULL,
            total_geral DECIMAL(10,2) DEFAULT 0.00,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            numero_transacao VARCHAR(20) UNIQUE,
            data DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
            ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",

        "Tabela contrato_carros" => "
        CREATE TABLE IF NOT EXISTS contrato_carros (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_contrato INT NOT NULL,
            id_carro INT NOT NULL,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            preco_viatura DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            FOREIGN KEY (id_contrato) REFERENCES contratos(id_contrato)
            ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (id_carro) REFERENCES carros(id_carro)
            ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",

        "Tabela capital_empresa" => "
        CREATE TABLE IF NOT EXISTS capital_empresa (
            id_capital INT AUTO_INCREMENT PRIMARY KEY,
            descricao VARCHAR(255) NOT NULL,
            id_servico INT DEFAULT NULL,
            id_contrato INT DEFAULT NULL,
            fluxo ENUM('entrada','saida') NOT NULL,
            valor DECIMAL(10,2) NOT NULL,
            data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_servico) REFERENCES servicos(id)
            ON DELETE SET NULL ON UPDATE CASCADE,
            FOREIGN KEY (id_contrato) REFERENCES contratos(id_contrato)
            ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",

        "Tabela facturas" => "
        CREATE TABLE IF NOT EXISTS facturas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_servico INT,
            nome_cliente VARCHAR(255),
            placa_carro VARCHAR(50),
            modelo_carro VARCHAR(100),
            endereco VARCHAR(255),
            total DECIMAL(10,2),
            data_factura DATETIME DEFAULT CURRENT_TIMESTAMP,
            numero_factura VARCHAR(20)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",

        "Tabela factura_itens" => "
        CREATE TABLE IF NOT EXISTS factura_itens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_factura INT,
            codigo_da_peca VARCHAR(100),
            peca_materia VARCHAR(255),
            quantidade INT,
            preco DECIMAL(10,2),
            subtotal DECIMAL(10,2)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",

        "Tabela recuperacao_conta" => "
        CREATE TABLE IF NOT EXISTS recuperacao_conta (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT,
            codigo VARCHAR(10),
            expiracao DATETIME,
            status ENUM('pendente','aprovado','usado') DEFAULT 'pendente'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        "

    ];

    foreach ($sqls as $nome => $query) {
        if (mysqli_query($conn, $query)) {
            $mensagens[] = "✅ " . $nome;
        } else {
            $erros[] = "❌ " . $nome . " => " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Instalador Oficina</title>
<style>
body {
    font-family: Arial;
    background: #f5f5f5;
    padding: 40px;
}
.box {
    background: white;
    padding: 30px;
    border-radius: 10px;
    max-width: 900px;
    margin: auto;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
.ok  { color: green; margin-bottom: 8px; }
.erro{ color: red;   margin-bottom: 8px; }
.btn {
    display: inline-block;
    padding: 15px 25px;
    background: #1d4ed8;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    margin-top: 20px;
}
</style>
</head>
<body>

<div class="box">

<h1>Instalador Oficina Capapelo</h1>
<p>Criação automática de todas as tabelas atualizadas.</p>

<?php
foreach ($mensagens as $m) echo "<div class='ok'>$m</div>";
foreach ($erros   as $e) echo "<div class='erro'>$e</div>";
?>

<a class="btn" href="?executar=1">EXECUTAR INSTALAÇÃO</a>

</div>

</body>
</html>