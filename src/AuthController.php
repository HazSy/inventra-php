<?php

declare(strict_types=1);

namespace App;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('/');
        }
        $this->view('auth/login', ['title' => 'Iniciar sesión']);
    }

    public function login(): void
    {
        $this->requireCsrf();

        $validator = Validator::make($_POST, [
            'email'    => 'required|email|max:190',
            'password' => 'required|min:6|max:72',
        ], ['email' => 'email', 'password' => 'contraseña']);

        if ($validator->fails()) {
            $this->back('/login', $validator->errors(), ['email' => $_POST['email'] ?? '']);
        }

        if (!Auth::attempt((string) $_POST['email'], (string) $_POST['password'])) {
            $this->back('/login', ['password' => 'Email o contraseña incorrectos.'], ['email' => $_POST['email'] ?? '']);
        }

        flash('success', 'Bienvenido de nuevo.');
        redirect('/');
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            redirect('/');
        }
        $this->view('auth/register', ['title' => 'Crear cuenta']);
    }

    public function register(): void
    {
        $this->requireCsrf();

        $validator = Validator::make($_POST, [
            'name'     => 'required|min:2|max:80',
            'email'    => 'required|email|max:190',
            'password' => 'required|min:8|max:72',
        ], ['name' => 'nombre', 'email' => 'email', 'password' => 'contraseña']);

        if ($validator->fails()) {
            $this->back('/register', $validator->errors(), ['name' => $_POST['name'] ?? '', 'email' => $_POST['email'] ?? '']);
        }

        $email = mb_strtolower(trim((string) $_POST['email']));
        $exists = Database::pdo()->prepare('SELECT 1 FROM users WHERE email = :email');
        $exists->execute(['email' => $email]);

        if ($exists->fetchColumn() !== false) {
            $this->back('/register', ['email' => 'Ya existe una cuenta con ese email.'], ['name' => $_POST['name'] ?? '', 'email' => $email]);
        }

        $statement = Database::pdo()->prepare(
            'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)'
        );
        $statement->execute([
            'name'     => trim((string) $_POST['name']),
            'email'    => $email,
            'password' => Auth::hash((string) $_POST['password']),
        ]);

        Auth::login((int) Database::pdo()->lastInsertId());
        flash('success', 'Cuenta creada. ¡Ya puedes gestionar tu inventario!');
        redirect('/');
    }

    public function logout(): void
    {
        $this->requireCsrf();
        Auth::logout();
        redirect('/login');
    }
}
