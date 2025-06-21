<?php
session_start();

if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}

require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    
    $diretorio = 'uploads/usuarios/';
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $tamanhoMaximo = 2 * 1024 * 1024;
    
    
    $check = getimagesize($_FILES['foto']['tmp_name']);
    if ($check === false) {
        header('Location: painel_admin.php?erro=tipo_arquivo_invalido');
        exit();
    }
    
   
    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    if (!in_array($extensao, $extensoesPermitidas)) {
        header('Location: painel_admin.php?erro=extensao_nao_permitida');
        exit();
    }
    
    
    if ($_FILES['foto']['size'] > $tamanhoMaximo) {
        header('Location: painel_admin.php?erro=arquivo_muito_grande');
        exit();
    }
    
    
    if (!file_exists($diretorio)) {
        mkdir($diretorio, 0777, true);
    }
    
    
    $nomeArquivo = uniqid() . '_' . basename($_FILES['foto']['name']);
    $caminhoCompleto = $diretorio . $nomeArquivo;
    
    
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminhoCompleto)) {
        try {
            $adminId = $_SESSION['admin_id'];
            $pdo = conexao();
            
            
            $stmt = $pdo->prepare("UPDATE administradores SET foto = ? WHERE id = ?");
            $stmt->execute([$caminhoCompleto, $adminId]);
            
           
            $_SESSION['admin_foto'] = $caminhoCompleto;
            
            
            $acao = "Foto de perfil atualizada";
            $log = $pdo->prepare("INSERT INTO logs_administrativos (admin_id, acao) VALUES (?, ?)");
            $log->execute([$adminId, $acao]);
            
            header('Location: painel_admin.php?sucesso=foto_alterada');
        } catch (PDOException $e) {
            
            if (file_exists($caminhoCompleto)) {
                unlink($caminhoCompleto);
            }
            header('Location: painel_admin.php?erro=erro_banco_dados');
        }
    } else {
        header('Location: painel_admin.php?erro=upload_foto');
    }
    exit();
}

header('Location: painel_admin.php');
?>