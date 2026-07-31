import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Reservations({ reservations }) {
    const [showCancelModal, setShowCancelModal] = useState(null);
    const { delete: destroy, processing } = useForm();

    const handleCancel = (reservationId) => {
        if (confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) {
            destroy(`/reservations/${reservationId}/cancel`, {
                onSuccess: () => setShowCancelModal(null),
            });
        }
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'confirmed':
                return 'bg-green-100 text-green-800';
            case 'checked_in':
                return 'bg-blue-100 text-blue-800';
            case 'checked_out':
                return 'bg-gray-100 text-gray-800';
            case 'cancelled':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getStatusLabel = (status) => {
        switch (status) {
            case 'confirmed':
                return 'Confirmée';
            case 'checked_in':
                return 'En cours';
            case 'checked_out':
                return 'Terminée';
            case 'cancelled':
                return 'Annulée';
            default:
                return status;
        }
    };

    const isCancellable = (reservation) => {
        return reservation.status === 'confirmed' && new Date(reservation.check_in) > new Date();
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Mes Réservations
                </h2>
            }
        >
            <Head title="Mes Réservations" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            {reservations.length === 0 ? (
                                <div className="text-center py-12">
                                    <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h3 className="mt-2 text-sm font-medium text-gray-900">Aucune réservation</h3>
                                    <p className="mt-1 text-sm text-gray-500">Vous n'avez pas encore de réservations.</p>
                                    <div className="mt-6">
                                        <Link
                                            href="/"
                                            className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                                        >
                                            Parcourir les chambres
                                        </Link>
                                    </div>
                                </div>
                            ) : (
                                <div className="space-y-4">
                                    {reservations.map((reservation) => {
                                        const room = reservation.room;
                                        const roomType = room?.room_type;
                                        const roomName = roomType?.name || `Chambre ${room?.room_number || reservation.id}`;
                                        
                                        return (
                                            <div key={reservation.id} className="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                                                <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                                    <div className="flex-1">
                                                        <div className="flex items-center gap-3 mb-2">
                                                            <h3 className="text-lg font-semibold text-gray-900">{roomName}</h3>
                                                            <span className={`px-3 py-1 text-xs font-semibold rounded-full ${getStatusColor(reservation.status)}`}>
                                                                {getStatusLabel(reservation.status)}
                                                            </span>
                                                        </div>
                                                        <div className="grid grid-cols-2 gap-4 text-sm text-gray-600 md:grid-cols-4">
                                                            <div>
                                                                <p className="text-gray-500">Arrivée</p>
                                                                <p className="font-medium">{new Date(reservation.check_in).toLocaleDateString('fr-FR')}</p>
                                                            </div>
                                                            <div>
                                                                <p className="text-gray-500">Départ</p>
                                                                <p className="font-medium">{new Date(reservation.check_out).toLocaleDateString('fr-FR')}</p>
                                                            </div>
                                                            <div>
                                                                <p className="text-gray-500">Prix total</p>
                                                                <p className="font-medium">{reservation.total_price} €</p>
                                                            </div>
                                                            <div>
                                                                <p className="text-gray-500">Réf.</p>
                                                                <p className="font-medium">#{reservation.id}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div className="flex gap-2">
                                                        {isCancellable(reservation) && (
                                                            <button
                                                                onClick={() => handleCancel(reservation.id)}
                                                                disabled={processing}
                                                                className="px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                            >
                                                                {processing ? 'Annulation...' : 'Annuler'}
                                                            </button>
                                                        )}
                                                        <Link
                                                            href={`/chambres/${room?.id}`}
                                                            className="px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 font-medium transition"
                                                        >
                                                            Voir la chambre
                                                        </Link>
                                                    </div>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
