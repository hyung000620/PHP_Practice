<link href="./css/join.css" rel="stylesheet">
<form method="post" action="joinProcess.php">
<diV class="container">
<? include 'header.php' ?>
    <table class="tbl_noline">
        <tbody>
            <tr>
                <th>회원명</th>
                <td>
                    <input type="text" name="user_name" id="user_name" placeholder="회원명(실명) 한글 또는 영문">
                </td>
            </tr>
            <tr>
                <th>아이디</th>
                <td>
                <input type="text" name="user_id" id="user_id" placeholder="영문 또는 숫자 (4~20자)" onchange="oncheck_id()">
                <input type="button" value="아이디 중복검사" onclick="check_id()">
                <span id="chid"></span>   
                </td>
            </tr>
            <tr>
                <th>비밀번호</th> <!-- 비밀번호 암호화 및 복호화-->
                <td>
                <input type="password" name="user_pw" id="user_pw" onchange="check_pw()" placeholder="영문, 숫자 특수문자 일부허용 (6~30자)">
                <span id="chpw"></span>
                </td>
            </tr>
            <tr>
                <th>비밀번호 확인</th>
                <td>
                <input type="password" name="user_pw2" id="user_pw2" onchange="check_pw()" placeholder="비밀번호 확인">
                </td>
            </tr>
            <tr>
                <th>이메일(선택)</th>
                <td>
                    <input type="text" name="user_email" id="user_email">
                    <span>
                        <label>
                            <input type="checkbox" name="r_mail">
                            <span>메일 수신동의</span>
                        </label>
                    </span>
                </td>
            </tr>
            <tr>
                <th>휴대폰</th>
                <td>
                    <input type="text" name="uesr_mobile" id="user_mobile" placeholder="-를 붙여주세요">
                    <span>
                        <label>
                            <input type="checkbox" name="r_mobile">
                            <span>문자 수신동의</span>
                        </label>
                    </span>
                </td>
            </tr>
            
            <tr>
                <th>주소</th>
                <td>
                    <input type="text" placeholder="우편번호" id="postcode" >
                    <input type="button" value="주소찾기" onclick="execDaumPostcode()"> 
                    <br>
                    <input type="text" placeholder="도로명 주소" id="roadAddress" name="user_address" >
                    <input type="text" placeholder="지번 주소" id="jibunAddress" >
                    <br>
                    <input type="text"  placeholder="상세주소" id="detailAddress" >
                </td>
            </tr>
        </tbody>
    </table>
    <input type="submit" value="회원가입">
</div>
</form>

<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script src="./js/join.js?ver=1"></script>