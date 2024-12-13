# Esquema SQL 

- La tabla se llamará users
- Tendrá una fila -> username | varchar
- Otra fila -> password | varchar

El username es único

## Consultas a la tabla (INSERT y GET)ç

Para un nuevo usuario:

```sql
INSERT INTO users (username, password ) VALUES("hola", "contraseña")
```

Para un SELECT:

```sql
SELECT id, password FROM users WHERE username = $username
```

## En php:

Simplemente meter eso en un $query 



Notas:

Sobre el hash de encriptado: https://www.php.net/manual/es/function.password-hash.php

Operador de coalescencia nula: https://www.php.net/manual/es/migration70.new-features.php#migration70.new-features.null-coalesce-op