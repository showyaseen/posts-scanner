const SelectInput = ({ options, value, onChange, allOptions, loading }) => (
	<div class="sui-form-field" >
		<select
			class="sui-select-lg sui-select-inline"
			value={value}
			onChange={(e) => onChange(e.target.value)} >
			{loading && <option> Loading ... </option>}
			{!loading && allOptions && <option value=''> {allOptions} </option>}
			{!loading && options.map(
				option => (
					<option key={option.value} value={option.value} > {option.label} </option>
				)
			)}
		</select>
	</div>
);

export default SelectInput;
