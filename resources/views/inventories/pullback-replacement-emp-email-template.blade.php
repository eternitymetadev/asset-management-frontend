<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Return of Company Asset Request</title>
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
				<h1>Return of Company Asset Request</h1>
			</div>
			<p><strong>Dear {{$emp_name}},</strong></p>
			<p class="indent">
				We kindly request the immediate return of a company asset that is
				currently assigned to you.
			</p>

			<p style="text-decoration: underline"><strong>Asset Details:</strong></p>
			<p style="padding-left: 1.5rem">
				<strong>Asset Description: </strong>{{$asset_category}} -  FRC-CHD-{{$un_id}}<br />
				<strong>Serial Number/Identification: </strong> {{$asset_sno}}
			</p>

			<p>
				Please arrange for the return of this asset to the HR department as soon
				as possible.
			</p>
			<p class="indent">
				If you have any questions or need assistance with the return process,
				please contact the HR department at [HR Contact Information].
			</p>
			<p class="indent">
				Thank you for your cooperation in promptly returning the company asset.
			</p>

			<p class="footer">
				<strong>Best regards,</strong><br />
				[HR Department]
			</p>
		</div>
	</body>
</html>