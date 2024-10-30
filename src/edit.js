import { __ } from '@wordpress/i18n';

import {
	TextControl,
	Dashicon
} from '@wordpress/components';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit( { attributes, setAttributes } ) {

	return (
		<div { ...useBlockProps( { className: 'components-placeholder is-large' } ) }>

			<div className="components-placeholder__label">
				<Dashicon
					icon="cart"
					style={{ fontSize: "24px", height: "24px", width: "24px", marginRight: "14px" }}
				/>
				{ __('Luway WooCommerce Upsale', 'luway-upsale') }
			</div>

			<div style={{ width: "100%" }}>

				<TextControl
					label={ __('Block title', 'luway-upsale') }
					value={ attributes.title }
					onChange={ ( title ) => setAttributes( { title } ) }
				/>

				<TextControl
					label={ __('Products:', 'luway-upsale') }
					type="number"
					value={ attributes.products }
					onChange={ ( products ) => setAttributes( { products: parseInt(products) } ) }
				/>

				<TextControl
					label={ __('Columns:', 'luway-upsale') }
					type="number"
					value={ attributes.columns }
					onChange={ ( columns ) => setAttributes( { columns: parseInt(columns) } ) }
				/>
			
			</div>

		</div>
	);
}
