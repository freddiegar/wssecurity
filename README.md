# Authentication PHP library

Valid Authentication WSSecurity

Use example

```
  try {
      $auth = new Authentication('username', 'passwordHash');
      
      if ($auth->isValid($password_in_db)) {
          // Authentication success
      }
  } catch (AuthenticationException $exception) {
      $error = $exception->getMessage();
  }
```
