# symfony-demo-messenger-postgres
Symfony demo application with messenger and postgres db

```bash
symfony serve --port=9074
```

or with ip 0.0.0.0 in order to expose to applications in docker

```bash
http://host.docker.internal:9074/
symfony serve --host=0.0.0.0 --port=9074
```

Create new message by command:

```bash
bin/console app:send-sms
```

or url:

```bash
/{_locale}/sms/notification
```

By sending message as HTTP request, "Messages" section in enabled in profiler

<img width="1220" height="768" alt="Zrzut ekranu z 2025-12-06 23-05-59" src="https://github.com/user-attachments/assets/9d19658c-a4bf-4508-8882-bf4b4d9c3978" />

