<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Asset Ready for Employee Handover</title>
		<style>
			/* Add your custom styles here */
			body {
				font-family: Arial, Helvetica, sans-serif;
				background-color: #fff;
				margin: 0;
				padding: 0;
				background: #fff;
			}
			.container {
				width: 600px;
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
			table {
				border-collapse: collapse;
				border-radius: 12px;
				width: 100%;
				border: 1px solid #555555;
				text-align: left;
				font-size: 14px;
			}
			th {
				padding: 4px 10px;
				width: 35%;
				border: 1px solid #555555;
			}
			td {
				padding: 4px 10px;
				border: 1px solid #555555;
				width: auto;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="header">
				<h1>Asset Ready for Employee Handover</h1>
			</div>
			<p><strong>Dear HR Department,</strong></p>
			<p class="indent">
				Laptop requested for Employee code: {{$emp_id}} and Employee Name: {{$emp_name}} from the IT department has been
				configured and is ready for handover. We kindly request your assistance
				in coordinating the handover process to the employee.
			</p>

			<p style="text-decoration: underline"><strong>Asset Details:</strong></p>
			<p style="padding-left: 1.5rem">
				<strong>Employee Name: </strong> {{$emp_name}}<br />
				<strong>Asset Type: </strong> {{$asset_category}}<br />
				<strong>Asset Model: </strong> {{$asset_model}}<br />
				<strong>Serial Number: </strong> {{$asset_sno}}
			</p>

			<p>
				The laptop has been prepared with the necessary software and
				permissions, ensuring that it is ready for immediate use. Thank you for
				your assistance in coordinating the handover process.
			</p>

			<p class="footer">
				<strong>Best regards,</strong><br />
				[IT Department]
			</p>
		</div>
	</body>
</html>
