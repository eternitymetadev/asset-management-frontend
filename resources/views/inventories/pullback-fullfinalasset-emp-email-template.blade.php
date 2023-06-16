<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Return of Company Assets - Urgent Action Required</title>
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
			table{
				border-collapse: collapse;
				border-radius: 12px;
				width: 100%;
				border: 1px solid #555555;
				text-align: left;
				font-size: 14px;
			}
			th{
				padding: 4px 10px;
				width: 35%;
				border: 1px solid #555555;

			}
			td{
				padding: 4px 10px;
				border: 1px solid #555555;
				width: auto;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="header">
				<h1>
					Return of Company Assets<br /><span
						style="font-size: 14px; line-height: 16px; color: red"
						>Urgent Action Required</span
					>
				</h1>
			</div>
			<p><strong>Dear {{$emp_name}},</strong></p>
			<p class="indent">
				As your employment with [Company Name] has ended, we kindly request your
				immediate attention to the return of company assets assigned to you
				during your tenure. Please review the list of assets below and take
				necessary action to return them as soon as possible:
			</p>

			<table>
				<tr>
					<th>Asset Code</th>
					<td>FRC-CHD-{{$un_id}}</td>
				</tr>
                <tr>
					<th>Asset Category</th>
					<td>{{$asset_category}}</td>
				</tr>
			</table>



			<p class="indent">
				We require all assets to be returned in good working condition, along
				with any accompanying accessories or peripherals provided. Prompt return
				of the assets is essential to ensure proper closure of your employment
				and facilitate a smooth transition.
			</p>
			<p class="indent">
				Please contact the HR department immediately at [HR Contact Information]
				to arrange for the return of the assets. You may choose to return them
				in person or utilize a trusted courier service. Kindly coordinate with
				our team to finalize the logistics and schedule for the asset return.
			</p>
			<p class="indent">
				Should you have any questions or require further assistance regarding
				the return process, do not hesitate to reach out to the HR department.
			</p>
			</p>
			<p class="indent">
				Your cooperation is highly appreciated, and we thank you in advance for
				 taking the necessary steps to return the company assets without delay.
			</p>

			<p class="footer">
				<strong>Best regards,</strong><br />
				[HR Department]
			</p>
		</div>
	</body>
</html>