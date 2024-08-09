const Notification = ({ id }) => (
	<div class="sui-floating-notices">
		<div
			role="alert"
			id={id}
			class="sui-notice sui-notice-lg"
			aria-live="assertive"
			width="800px"
		/>
	</div>
);

export default Notification;
