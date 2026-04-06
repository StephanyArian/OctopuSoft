import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import HomePage from './Pages/HomePage';

const root = createRoot(document.getElementById('root'));
root.render(<HomePage />);