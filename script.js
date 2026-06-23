
function showWelcome() {
    alert("Selamat Datang di MoneyTracker! Mari mulai kelola keuangan Anda dengan lebih cerdas.");
}

// Efek Scroll Halus (Smooth Scroll) untuk navigasi
document.querySelectorAll('.nav-links a').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        document.querySelector(targetId).scrollIntoView({
            behavior: 'smooth'
        });
    });
});


window.addEventListener('scroll', () => {
    const header = document.querySelector('header');
    if (window.scrollY > 50) {
        header.style.padding = '10px 8%';
    } else {
        header.style.padding = '15px 8%';
    }
});

// LOGIN VALIDATION

const loginForm = document.getElementById('loginForm');

if(loginForm){

    const email =
    document.getElementById('email');

    const password =
    document.getElementById('password');

    const agree =
    document.getElementById('agree');

    loginForm.addEventListener('submit', function(e){

        e.preventDefault();

        let valid = true;

        const emailError =
        document.getElementById('emailError');

        const passwordError =
        document.getElementById('passwordError');

        const agreeError =
        document.getElementById('agreeError');

        const successMsg =
        document.getElementById('successMsg');

        emailError.textContent = '';
        passwordError.textContent = '';
        agreeError.textContent = '';
        successMsg.textContent = '';

        const emailPattern =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if(email.value.trim()===''){
            emailError.textContent =
            'Email wajib diisi';
            valid = false;
        }
        else if(
            !emailPattern.test(email.value)
        ){
            emailError.textContent =
            'Format email tidak valid';
            valid = false;
        }

        if(password.value.length < 8){
            passwordError.textContent =
            'Password minimal 8 karakter';
            valid = false;
        }

        if(!agree.checked){
            agreeError.textContent =
            'Centang persetujuan';
            valid = false;
        }

        if(valid){
            localStorage.setItem(
                'userEmail',
                email.value
            );

            successMsg.textContent =
            'Login berhasil!';

            setTimeout(()=>{
                window.location.href =
                'dashboard.html';
            },1000);
        }
    });

    // realtime validation
    email.addEventListener('input',()=>{
        document.getElementById(
        'emailError').textContent='';
    });

    password.addEventListener('input',()=>{
        document.getElementById(
        'passwordError').textContent='';
    });
}

// DASHBOARD LOGIN

const userEmail =
document.getElementById(
'userEmail'
);

if(userEmail){

    const email =
    localStorage.getItem(
    'userEmail'
    );

    if(email){
        userEmail.textContent =
        email;
    }
    else{
        window.location.href =
        'login.html';
    }
}

function logout(){

    localStorage.removeItem(
    'userEmail'
    );

    window.location.href =
    'login.html';
}

// DASHBOARD TRANSACTION

const form =
document.getElementById(
'transactionForm'
);

if(form){

    const balance =
    document.getElementById(
    'balance'
    );

    const income =
    document.getElementById(
    'income'
    );

    const expense =
    document.getElementById(
    'expense'
    );

    const list =
    document.getElementById(
    'transactionList'
    );

    let transactions =
    JSON.parse(
        localStorage.getItem(
        'transactions'
        )
    ) || [];

    function saveData(){

        localStorage.setItem(
            'transactions',
            JSON.stringify(
                transactions
            )
        );
    }

    function render(){

        list.innerHTML='';

        let total=0;
        let totalIncome=0;
        let totalExpense=0;

        transactions.forEach(
        (trx,index)=>{

            const li =
            document.createElement(
            'li'
            );

            li.className=
            `transaction-item ${trx.type}`;

            li.innerHTML=`
                <div>
                    <strong>${trx.title}</strong><br>
                    ${trx.date}
                </div>

                <div>
                    Rp ${Number(
                    trx.amount
                    ).toLocaleString()}
                </div>

                <button
                class="delete-btn"
                onclick="deleteTransaction(${index})">
                    Hapus
                </button>
            `;

            list.appendChild(li);

            if(
                trx.type==='income'
            ){
                totalIncome +=
                Number(trx.amount);
                total +=
                Number(trx.amount);
            }else{
                totalExpense +=
                Number(trx.amount);
                total -=
                Number(trx.amount);
            }

        });

        balance.textContent=
        'Rp '+
        total.toLocaleString();

        income.textContent=
        'Rp '+
        totalIncome.toLocaleString();

        expense.textContent=
        'Rp '+
        totalExpense.toLocaleString();
    }

    form.addEventListener(
    'submit',
    function(e){

        e.preventDefault();

        const title =
        document.getElementById(
        'title'
        ).value;

        const amount =
        document.getElementById(
        'amount'
        ).value;

        const date =
        document.getElementById(
        'date'
        ).value;

        const type =
        document.getElementById(
        'type'
        ).value;

        transactions.push({
            title,
            amount,
            date,
            type
        });

        saveData();
        render();
        form.reset();
    });

    window.deleteTransaction=
    function(index){

        transactions.splice(
        index,
        1
        );

        saveData();
        render();
    }

    render();
}