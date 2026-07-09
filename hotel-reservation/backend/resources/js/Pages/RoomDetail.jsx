import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '../Layouts/MainLayout';

export default function RoomDetail({ room }) {
    const roomType = room.room_type;
    const tariff = roomType?.tariffs?.[0];
    const price = tariff?.price || "---";
    const name = roomType?.name || `Chambre #${room.room_number}`;
    const description = roomType?.description || "Une magnifique chambre équipée de tout le confort nécessaire pour un séjour inoubliable.";

    return (
        <MainLayout>
            <Head title={name} />
            <div className="py-8">
                <Link href="/" className="text-blue-600 hover:underline mb-6 inline-block font-medium">
                    &larr; Retour à l'accueil
                </Link>
                
                <div className="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
                    <div>
                        <h1 className="text-3xl md:text-4xl font-bold text-gray-900">{name}</h1>
                    </div>
                    <div className="text-2xl font-bold text-blue-600">
                        {price} € <span className="text-base font-normal text-gray-500">/ nuit</span>
                    </div>
                </div>

                {/* Galerie d'images */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                    <div className="col-span-1 md:col-span-2 h-96 bg-gray-200 rounded-2xl flex items-center justify-center border border-gray-100">
                        <span className="text-gray-400 font-medium">Photo Principale</span>
                    </div>
                    <div className="flex flex-col gap-4">
                        <div className="h-44 bg-gray-200 rounded-2xl flex items-center justify-center border border-gray-100">
                            <span className="text-gray-400 font-medium">Photo 2</span>
                        </div>
                        <div className="h-44 bg-gray-200 rounded-2xl flex items-center justify-center border border-gray-100">
                            <span className="text-gray-400 font-medium">Photo 3</span>
                        </div>
                    </div>
                </div>

                {/* Informations */}
                <div className="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-8 justify-between items-start">
                    <div className="max-w-2xl">
                        <h2 className="text-2xl font-semibold mb-4 text-gray-800">À propos de cette chambre</h2>
                        <p className="text-gray-600 leading-relaxed text-lg">{description}</p>
                    </div>
                    <div className="w-full md:w-auto shrink-0 bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <button className="w-full px-8 py-3 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition font-bold text-lg">
                            Réserver maintenant
                        </button>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
