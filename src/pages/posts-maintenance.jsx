import { createRoot } from '@wordpress/element';
import { useState, useEffect } from 'react';
import PostAPI from './api/post-api';
import { __ } from '@wordpress/i18n';
import { Notification, SelectInput, Button } from './components';
import '@wpmudev/shared-ui/dist/js/_src/notifications.js';
import "./scss/style.scss"

const domElement = document.getElementById(window.ytahaPostScanner.domElementId);

const PostsMaintenance = () => {
	const notificationId = 'ytaha-sui-notification';
	const [postType, setPostType] = useState('post');
	const { postLoading, postTypes, getPostTypes, scanLoading, scanResult, scanPosts } = PostAPI();

	useEffect(() => {
		getPostTypes();
	}, []);

	useEffect(() => {
		let message = '';
		let status = 'error';
		if (scanResult?.data?.status && 200 !== scanResult.data.status) {
			message = `<p> ${scanResult.message} </p>`;
		} else if (scanResult?.message && '' !== scanResult.message) {
			status = 'success';
			message = `<p> ${scanResult.message} </p>`;
		}

		message && SUI.openNotice(notificationId, message, { type: status });

	}, [scanResult]);

	return (
		<>
			<div class="sui-header">
				<h1 class="sui-header-title">
					{__('Posts Maintenance', 'ytaha-posts-scanner')}
				</h1>
			</div>

			<div className="sui-box">

				<div className="sui-box-header">
					<h2 className="sui-box-title">{__('Schedule Posts Scan', 'ytaha-posts-scanner')}</h2>
				</div>
				<div className="sui-box-body">

					<Notification id={notificationId} />

					<div className="sui-box-settings-row">
						<div class="sui-margin-top">
							<SelectInput
								value={postType}
								allOptions={__('All Types', 'ytaha-posts-scanner')}
								options={postTypes}
								onChange={setPostType}
								loading={postLoading}
							/>
						</div>

						<div class="sui-margin">
							<Button
								onClick={() => scanPosts(postType)}
								loading={scanLoading}
								loadingLabel={__('Scanning...', 'ytaha-posts-scanner')}
								label={__('Scan Posts', 'ytaha-posts-scanner')}
							/>
						</div>
					</div>

					<div className="sui-row">
						<p> {__('When clicked, this button should scan all public posts and pages (with customizable post type filters) and update the `ytaha_test_last_scan` post_meta with the current timestamp. Ensure that operation will keep running if the user leaves that page. This operation should be repeated daily to ensure ongoing maintenance.', 'ytaha-posts-scanner')}</p>
					</div>

				</div>
			</div>
		</>
	);
};

createRoot(domElement).render(<PostsMaintenance />);


