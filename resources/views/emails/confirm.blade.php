Hello {{$user->name}}
이메일이 변경되었습니다. 아래 링크를 눌러 인증을 다시 해주세요:
{{route('verify', $user->verification_token)}}
