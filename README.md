UnispacePro

**Equipo de Desarrollo:** Oscar (Líder), Greymel (Co-líder y Fullstack), Anderson (Base de Datos) y Diego (Backend).

## Requisitos Previos para correr en otra PC
Para que este proyecto funcione en otra computadora, asegúrate de tener instalado:
1. **XAMPP** o PHP 8.1+
2. **Composer** (Gestor de paquetes de PHP)

## Instrucciones de Instalación
Sigue estos pasos si acabas de descomprimir el proyecto en una computadora nueva:

1. **Abre una terminal** en la carpeta raíz del proyecto (`unispacepro`).
2. **Instala las dependencias de Laravel** (Dado que la carpeta `vendor` no se incluye para ahorrar espacio):
   ```bash
   composer install
   ```
3. **Copia el archivo de entorno** (Si no existe un `.env`, copia el de ejemplo):
   ```bash
   cp .env.example .env
   ```
4. **Genera la llave de la aplicación**:
   ```bash
   php artisan key:generate
   ```
5. **Prepara la Base de Datos** (Asegúrate de que la extensión SQLite esté habilitada en tu `php.ini`):
   ```bash
   php artisan migrate
   ```
   *(Nota: La base de datos ya viene incluida en `database/database.sqlite` con los datos de prueba, así que este paso podría no ser estrictamente necesario si el archivo .sqlite se copió intacto).*

6. **Enlaza el almacenamiento de fotos de perfil**:
   ```bash
   php artisan storage:link
   ```

7. **Inicia el Servidor Local**:
   ```bash
   php artisan serve
   ```

El proyecto estará corriendo en `http://127.0.0.1:8000`.

## Cuentas de Prueba
- Puedes registrarte con un correo nuevo.
- O usar las cuentas existentes en la base de datos de SQLite incluida.
-Cuentas de prueba:
1. ADMINISTRADOR:
   - Cedula:00000001
   - Correo:admin@unispace.edu.ve
   - Contraseña:Admin1234!

2. PROFESORES (Prof1234!):
   - Cedula: 12345678 | mgonzalez@unispace.edu.ve (Prof. Maria Gonzalez)
   - Cedula: 23456789 | jramirez@unispace.edu.ve  (Prof. Jose Ramirez)
   - Cedula: 34567890 | atorres@unispace.edu.ve   (Prof. Ana Torres)
   - Cedula: 45678901 | lmendoza@unispace.edu.ve  (Prof. Luis Mendoza)
   - Cedula: 56789012 | esuarez@unispace.edu.ve   (Prof. Elena Suarez)
   - Cedula: 67890123 | srios@unispace.edu.ve     (Prof. Sofia Rios)
   - Cedula: 78901234 | jlopez@unispace.edu.ve    (Prof. Javier Lopez)
   - Cedula: 89012345 | pgomez@unispace.edu.ve    (Prof. Patricia Gomez)

3. ESTUDIANTES (Est1234!):
   - Cedula: 28100001 | aperez@gmail.com          (Andres Perez)
   - Cedula: 28100002 | lmartinez@gmail.com       (Luisa Martinez)
   - Cedula: 28100003 | dflores@gmail.com         (Diego Flores)
