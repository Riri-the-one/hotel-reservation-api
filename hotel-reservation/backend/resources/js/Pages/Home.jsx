import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '../Layouts/MainLayout';

export default function Home({ rooms }) {
    return (
        <MainLayout>
            <Head title="Accueil" />
            
            <div className="flex flex-col items-center text-center py-16">
                <h1 className="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Trouvez la chambre idéale
                </h1>
                <p className="text-gray-600 text-lg mb-8 max-w-2xl">
                    Gérez vos réservations, vos clients et vos disponibilités en temps réel grâce à notre nouvelle plateforme.
                </p>
            </div>

            {/* Section des chambres */}
            <div className="pb-12">
                <h2 className="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Nos Chambres Disponibles</h2>
                
                {rooms && rooms.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {rooms.map((room) => {
                            const roomType = room.room_type;
                            const tariff = roomType?.tariffs?.[0];
                            const price = tariff?.price || "---";
                            
                            return (
                                <div key={room.id} className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                                    <div className="h-48 bg-gray-200 flex items-center justify-center">
                                        <span className="text-gray-400">Image de la chambre</span>
                                    </div>
                                    <div className="p-5">
                                        <h3 className="text-xl font-semibold text-gray-900 mb-2">
                                            {roomType?.name || `Chambre #${room.room_number}`}
                                        </h3>
                                        <p className="text-gray-500 mb-4 line-clamp-2">
                                            {roomType?.description || "Une magnifique chambre équipée de tout le confort nécessaire pour un séjour inoubliable."}
                                        </p>
                                        <div className="flex justify-between items-center mt-4 pt-4 border-t border-gray-50">
                                            <span className="text-lg font-bold text-blue-600">
                                                {price} € <span className="text-sm font-normal text-gray-500">/ nuit</span>
                                            </span>
                                            <Link href={`/chambres/${room.id}`} className="px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-600 hover:text-white transition font-medium">
                                                Voir et Réserver
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                ) : (
                    <div className="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                        <p className="text-gray-500">Aucune chambre n'est disponible dans la base de données pour le moment.</p>
                    </div>
                )}
            </div>
        </MainLayout>
    );
}