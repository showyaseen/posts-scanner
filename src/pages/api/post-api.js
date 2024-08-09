import { useState } from 'react';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

import cache from '../utils/cache';

/**
 * Hook to interact with post-related APIs.
 *
 * @return {Object} Contains functions and state variables related to posts api.
 */
const PostAPI       = () => {
    const CACHE_KEY = 'post_types';

    // State variables for post loading, types, scan loading, and scan result
    const [postLoading, setPostLoading] = useState(false);
    const [postTypes, setPostTypes]     = useState([]);
    const [scanLoading, setScanLoading] = useState(false);
    const [scanResult, setScanResult]   = useState('');

    /**
     * Fetches the available post types.
     */
    const getPostTypes = async() => {
        // Check the cache first if exsits.
        const cachedPostTypes = cache.getCache(CACHE_KEY);
        if (null !== cachedPostTypes) {
            setPostTypes(cachedPostTypes);
            return;
        }

        if (postLoading) {
            return;
        }

        setPostLoading(true);

        // Fetch post types from wp_json posts api and cache the result.
        apiFetch({ url: window.ytahaPostScanner.restEndpointPostTypes })
        .then(
            (types) => {
                // Map received post types to label-value objects
                const typeOptions = Object.keys(types).map(
                    (type) => ({
                        label: types[type].name,
                        value: type,
                    })
                );
            setPostTypes(typeOptions);
            cache.setCache(CACHE_KEY, typeOptions);
            }
        )
    .catch(
        (error) => {
        if (error.code && error.message) {
            setScanResult(
            {
                data: { status: error.code },
                message: error.message,
            }
            );
        }
        }
    )
    .finally(
        () => {
        setPostLoading(false);
        }
    );
    };

    /**
     * Initiates a scan for posts of a specific type.
     *
     * @param {string} postType The post type to scan.
     */
    const scanPosts = async(postType) => {
        if (scanLoading) {
            return;
        }

        setScanLoading(true);

        const data = { post_type: postType };

        apiFetch(
            {
                url: window.ytahaPostScanner.restEndpointPostsScan,
                method: 'POST',
                data: data,
            }
        )
    .then(
        (response) => {
        if (response.status === 'success') {
            setScanResult(
            {
                data: { status: 200 },
                message: __('Scan started successfully!', 'ytaha-posts-scanner'),
                }
            );
        } else {
                setScanResult(
            {
                    data: { status: 422 },
                    message: __('An error occurred: ' + response.message, 'ytaha-posts-scanner'),
                        }
        );
        }
        }
    )
    .catch(
        (error) => {
        if (error.code && error.message) {
            setScanResult(
            {
                data: { status: error.code },
                message: error.message,
            }
                );
        } else {
                setScanResult(
            {
                    data: { status: 400 },
                    message: __('An unexpected error occurred.', 'ytaha-posts-scanner'),
                        }
        );
        }
        }
    )
    .finally(
        () => {
        setScanLoading(false);
        }
    );
    };

    return { postLoading, postTypes, getPostTypes, scanLoading, scanResult, scanPosts };
};

export default PostAPI;
