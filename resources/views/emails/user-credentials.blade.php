<div style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2>Доступ в LMS-Мир</h2>

    <p>Здравствуйте, {{ $user->name }}.</p>

    <p>Для вас создана учётная запись в системе LMS-Мир.</p>

    <p>
        <strong>Логин:</strong> {{ $user->email }}<br>
        <strong>Пароль:</strong> {{ $plainPassword }}
    </p>

    <p>После первого входа рекомендуется сменить пароль в профиле.</p>
</div>