<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>{{$asset_category}} Handover Confirmation</title>
		<style>
			/* Add your custom styles here */
			body {
				font-family: Arial, Helvetica, sans-serif;
				background-color: #fff;
				margin: 0;
				padding: 0;
			}
			.container {
				max-width: 600px;
				margin: 0 auto;
				padding: 20px;
				background-color: #ffffff;
				border-radius: 6px;
				box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
			}
			.header {
				padding: 1rem;
				line-height: 2rem;
				/* border-bottom: 2px solid; */
				color: #0066cc;
				margin-bottom: 2rem;
			}
			h1 {
				margin: 0;
				font-size: 28px;
				text-align: center;
			}
			p {
				color: #555555;
				margin-bottom: 16px;
				line-height: 22px;
				font-size: 14px;
			}
			p.indent {
				text-indent: 1.5rem;
			}
			p:has(a) {
				text-align: center;
			}
			a {
				color: #ffffff;
				border-radius: 10px;
				text-decoration: none;
				margin: 1rem;
				background-color: #0066cc;
				padding: 10px 20px;
				display: inline-block;
				font-size: 1rem;
			}
			a:hover {
				background-color: #004080;
			}
			.footer {
				margin-top: 4rem;
				font-size: 1rem;
				border-top: 1px solid #ababab;
				padding-top: 1rem;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="header">
				<h1>{{$asset_category}} Handover Confirmation</h1>
			</div>
			<p><strong>Dear {{$emp_name}},</strong></p>
			<p class="indent">
				We are pleased to inform you that a asset FRC-CHD-{{$un_id}} has been assigned to you.
				Please click on the link provided below to complete the Laptop Handover
				Confirmation.
			</p>
			<p>
				<a href="{{ url('api/accept-asset/'.$invoice_id)}}" target="_blank">Click Here for Confirmation</a>
			</p>
			<p class="indent">
				By accessing the link, you will find the necessary information regarding
				the laptop assigned to you. Kindly review the details and proceed to
				complete the online undertaking. This confirmation signifies your
				acceptance of responsibility for the laptop and compliance with the
				company's IT policies and guidelines.
			</p>
			<p class="indent">
				Should you have any questions or encounter any difficulties during the
				confirmation process, please contact the HR department for assistance.
			</p>
			<p class="indent">Thank you for your prompt attention to this matter.</p>

			<p class="footer">
				<strong>Best regards,</strong><br />
				[HR Department]
			</p>
		</div>
	</body>
</html>