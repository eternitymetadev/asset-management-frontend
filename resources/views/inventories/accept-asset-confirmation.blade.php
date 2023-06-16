<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Confirmation Page</title>

    <style>
    * {
        box-sizing: border-box;
        padding: 0;
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
    }

    main {
        width: 100%;
        max-width: 1100px;
        margin-inline: auto;
        padding: 1rem;
        background-image: url(./header-bg.svg);
        background-position: right top;
        background-repeat: no-repeat;
        background-size: 820px 150px;
        min-height: 100vh;
        box-shadow: 0 0 17px -4px rgba(131, 131, 131, 0.4392156863);
    }

    main .indent {
        text-indent: 2rem;
    }

    main header {
        display: flex;
        align-items: flex-end;
        max-width: 900px;
        margin: auto;
    }

    main header .logo {
        flex: 1;
    }

    main header .logo img {
        height: 70px;
        margin-left: 2rem;
    }

    main header .companyInfo {
        text-align: right;
        color: #fff;
    }

    main header .companyInfo .name {
        font-size: 24px;
        font-weight: bold;
        line-height: 40px;
    }

    main .content {
        font-size: 15px;
        line-height: 24px;
        max-width: 900px;
        margin: auto;
        padding: 1rem;
    }

    main .content .date {
        margin: 4rem auto 2rem;
        text-align: right;
        font-size: 1rem;
        font-weight: 500;
        text-decoration: underline;
    }

    main .content .employeeDetail {
        margin: 5rem auto 2rem;
    }

    main .content .employeeDetail .empBlock .empName {
        font-weight: 600;
    }

    main .content ul {
        margin: 2rem;
        color: #040404;
    }

    main .content ul li span {
        font-weight: 600;
        color: #005513;
    }

    main .content .checkbox {
        padding: 2rem 1rem;
        margin: 8px;
        text-align: center;
    }

    main .content .checkbox a {
        background: #005513;
        color: #fff;
        font-size: 1.2rem;
        padding: 8px 32px;
        border-radius: 12px;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
        text-decoration: none;
    }

    main .content .checkbox input {
        display: none;
    }

    main .content .checkbox label {
        background: #005513;
        color: #fff;
        font-size: 1.2rem;
        padding: 8px 32px;
        border-radius: 12px;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
    }

    main .content .thankYouLine {
        margin-top: 3rem;
    }

    main .content .footer {
        margin-top: 1rem;
    }

    main .content .footer p:first-child {
        font-weight: bold;
    }

    /*# sourceMappingURL=style.css.map */
    </style>
</head>

<body>
    <main>
        <header>
            <div class="logo">
                <img src="{{asset('images/logo.svg')}}" alt="company logo" />
            </div>

            <div class="companyInfo">
                <p class="name">Frontiers Agrotech Pvt Ltd</p>
                <p class="address">B-103 Bestech Business Tower</p>
                <p class="pin">Mohali, Punjab 160062</p>
            </div>
        </header>

        <div class="content">
            <p class="date">Date: 12 Jan 2023</p>
            <div class="employeeDetail">
                <p>Dear</p>
                <div class="empBlock">
                    <p class="empName">Employee Name</p>
                    <p class="empAddress">#800, Gali No 1, Adarsh Colony, Azad Nagar</p>
                    <p class="empAddress">Hisar, Haryana 125001</p>
                </div>
            </div>

            <p class="indent">
                Congratulations on receiving the assigned laptop. Before proceeding,
                please carefully read and understand the following terms and
                conditions associated with the laptop handover:
            </p>

            <ul>
                <li class="point">
                    <span>Responsibility: </span> I acknowledge that I have received the
                    assigned laptop and assume full responsibility for its safekeeping
                    and proper use. I will ensure that the laptop is used solely for
                    company-related tasks and will protect it from loss, damage, or
                    theft.
                </li>
                <li class="point">
                    <span>Compliance: </span> I agree to comply with all applicable
                    company IT policies and guidelines regarding the use and security of
                    the laptop. This includes adhering to software usage policies, data
                    protection measures, and security protocols.
                </li>
                <li class="point">
                    <span>Reporting: </span>I will promptly report any loss, damage, or
                    theft of the laptop to the IT department and the HR department.
                    Failure to report such incidents may result in disciplinary action.
                </li>
                <li class="point">
                    <span>Compensation: </span> In the event of loss, damage, or theft
                    of the laptop due to negligence or non-compliance with company
                    policies, I understand that I will be responsible for compensating
                    the company for the full replacement or repair costs.
                </li>
            </ul>

            <p class="indent">
                By clicking the "Accept" button below, I acknowledge that I have read
                and understood the terms and conditions outlined above. I agree to
                comply with these terms and accept the associated responsibilities.
            </p>

            <div class="checkbox">
            <a href="{{ url('api/approved-asset/'.Crypt::encrypt($invoice_id))}}" target="_blank">
                Accept
            </a>
                <!-- <input type="checkbox" name="agree" id="agree" />
					<label for="agree">Accept</label> -->
            </div>

            <p class="indent">
                If you have any questions or concerns regarding these terms and
                conditions, please contact the HR department for clarification.
            </p>
            <p class="thankYouLine">Thank you for your cooperation.</p>
            <div class="footer">
                <p>Thanks & regards!</p>
                <p>HR Department, Frontiers</p>
            </div>
        </div>
    </main>
</body>

</html>