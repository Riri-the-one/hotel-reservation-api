import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

export default function ReservationConfirmation({ reservation }) {
    const room = reservation.room;
    const roomType = room?.room_type;
    const roomName = roomType?.name || `Chambre ${room?.room_number || reservation.id}`;
    const roomNumber = room?.room_number || 'N/A';
    
    return (
        <MainLayout>
            <Head title="Réservation Confirmée" />
            
            <div className="py-12 max-w-4xl mx-auto px-4">
                <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    {/* Success Header */}
                    <div className="bg-green-50 p-8 text-center border-b border-green-100">
                        <div className="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                            <svg className="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h1 className="text-3xl font-bold text-gray-900 mb-2">Réservation Confirmée !</h1>
                        <p className="text-gray-600">Votre réservation a été enregistrée avec succès.</p>
                    </div>

                    {/* Reservation Details */}
                    <div className="p-8">
                        <h2 className="text-xl font-semibold text-gray-800 mb-6">Détails de votre réservation</h2>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div className="bg-gray-50 p-4 rounded-lg">
                                <p className="text-sm text-gray-500 mb-1">Numéro de réservation</p>
                                <p className="font-semibold text-gray-900">#{reservation.id}</p>
                            </div>
                            <div className="bg-gray-50 p-4 rounded-lg">
                                <p className="text-sm text-gray-500 mb-1">Chambre</p>
                                <p className="font-semibold text-gray-900">{roomName} - {roomNumber}</p>
                            </div>
                            <div className="bg-gray-50 p-4 rounded-lg">
                                <p className="text-sm text-gray-500 mb-1">Date d'arrivée</p>
                                <p className="font-semibold text-gray-900">{new Date(reservation.check_in).toLocaleDateString('fr-FR')}</p>
                            </div>
                            <div className="bg-gray-50 p-4 rounded-lg">
                                <p className="text-sm text-gray-500 mb-1">Date de départ</p>
                                <p className="font-semibold text-gray-900">{new Date(reservation.check_out).toLocaleDateString('fr-FR')}</p>
                            </div>
                        </div>

                        <div className="bg-blue-50 p-6 rounded-lg mb-8">
                            <div className="flex justify-between items-center">
                                <div>
                                    <p className="text-sm text-blue-600 mb-1">Prix total</p>
                                    <p className="text-2xl font-bold text-blue-900">{reservation.total_price} €</p>
                                </div>
                                <div className="text-right">
                                    <p className="text-sm text-blue-600 mb-1">Statut</p>
                                    <span className="inline-block px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                                        {reservation.status === 'confirmed' ? 'Confirmée' : reservation.status}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {/* Actions */}
                        <div className="flex flex-col sm:flex-row gap-4">
                            <Link 
                                href="/profile/reservations" 
                                className="flex-1 text-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition"
                            >
                                Voir mes réservations
                            </Link>
                            <Link 
                                href="/" 
                                className="flex-1 text-center px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition"
                            >
                                Retour à l'accueil
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
