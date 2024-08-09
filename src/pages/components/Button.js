const Button = ({ loading, label, loadingLabel = label, onClick }) => (
	<div class="sui-form-field" >
		<button
			onClick={onClick}
			disabled={loading}
			class="sui-button sui-button-lg sui-button-blue" >

			{loading ? loadingLabel : label}

		</button>
	</div>
);

export default Button;
