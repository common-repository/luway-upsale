import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { RawHTML } from '@wordpress/element';

export default function save({ attributes }) {

	const shortcode = `[wcupsale products="` + attributes.products + `" columns="` + attributes.columns + `"]`;

	return (
		<div { ...useBlockProps.save({ className: 'woocommerce' }) }>
			<RichText.Content
				tagName="h2"
				value={ attributes.title }
			/>
			<RawHTML>{ shortcode }</RawHTML>
		</div>
	);
}
