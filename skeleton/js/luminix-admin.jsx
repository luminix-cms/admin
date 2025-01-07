import React from 'react';
import ReactDOM from 'react-dom/client';
import { LuminixCms } from '@luminix/mui-cms';

import '@fontsource/roboto/300.css';
import '@fontsource/roboto/400.css';
import '@fontsource/roboto/500.css';
import '@fontsource/roboto/700.css';

import '@luminix/react/css/ReactForms.css';

import AppServiceProvider from './Providers/AppServiceProvider';

const container = document.getElementById('root');

if (!container) {
    throw new Error('Failed to find the root element');
}

ReactDOM.createRoot(container).render(
    <React.StrictMode>
        <LuminixCms
            providers={[
                AppServiceProvider,
            ]}
        />
    </React.StrictMode>
);
