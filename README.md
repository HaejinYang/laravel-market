#

## 문제 해결
### 외래키 참조 타입 문제
라라벨 Eloquent Model은 ORM으로 PHP 스크립트에서 DB에 관여하지 않고도 데이터를 관리할 수 있게 도와준다. 다만, 이것은 DB에 관여하는 코드를 분리하여 감춘 것이지, 코드 자체가 존재하지 않는 것은 아니다.
이러한 코드가 존재하는 곳은 `database/migrations`의 마이그레이션용 파일이다.  
`Schema` 파사드와 `Blueprint`를 이용하여 테이블을 정의한 것을 볼 수 있다. 테이블엔 보통 레코드를 유일하게 식별하기 위한 key가 존재한다.
Blueprint에선 `id()`메소드를 지원하여 키를 생성할 수 있게 해준다. 이 메소드의 설명을 보면 자동 증가하는 8byte big integer라 되어 있다. 
이 정보를 토대로 다른 테이블에서 id를 참조할 때 타입으로 big integer를 사용하면 참조키 타입 에러가 발생한다. 왜냐하면 테이블엔 `unsigned`속성까지 지정되어 있기 떄문이다.

### 다른 모델을 상속받아 사용했을 때 테이블이 없는 문제
User 모델을 상속받는 Seller, Buyer 모델은 이것들을 위한 테이블이 필요하지 않다. users 테이블을 그대로 사용하면 되는 것이다.
하지만, Eloquent Model에서 암묵적으로 모델 클래스 이름을 바탕으로 테이블에 접근한다. User 모델은 users, Seller 모델은 sellers 테이블로 매핑된다.
따라서 추가 조치가 필요하다. 명시적으로 모델과 테이블을 매핑해야하는 것이다. 이를 위해 모델에 `$table`프로퍼티를 재정의하는 방법이 존재한다. 따라서 User모델에 $table 프로퍼티를 재정의하면,
User 모델을 상속받아 사용하는 Seller, Buyer 모두 재정의한 테이블에 매핑되어 테이블이 없는 문제를 해결할 수 있다.
