Hello {{$user->name}}
계정이 생성 되었습니다. 이 링크를 눌러서 인증을 해주세요:
{{route('verify', $user->verification_token)}}
