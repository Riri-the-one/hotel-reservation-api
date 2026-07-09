import React from 'react';
import { Link } from '@inertiajs/react';

export default function MainLayout({ children }) {
    return (
        <div className="min-h-screen bg-gray-50 flex flex-col">
            <nav className="bg-white shadow-md p-4">
                <div className="max-w-7xl mx-auto flex justify-between items-center">
                    <Link href="/" className="text-xl font-bold text-blue-600">
                        HôtelRésa
                    </Link>
                    <div className="space-x-6">
                        <Link href="/" className="text-gray-600 hover:text-blue-600 transition">Accueil</Link>
                        <Link href="#" className="text-gray-600 hover:text-blue-600 transition">Chambres</Link>
                        <Link href="#" className="text-gray-600 hover:text-blue-600 transition">Réservations</Link>
                    </div>
                </div>
            </nav>

            <main className="flex-grow max-w-7xl mx-auto w-full p-6">
                {children}
            </main>

            <footer className="bg-gray-800 text-white text-center p-4 mt-auto">
                <p>&copy; 2026 - Application de Réservation</p>
            </footer>
        </div>
    );
}
