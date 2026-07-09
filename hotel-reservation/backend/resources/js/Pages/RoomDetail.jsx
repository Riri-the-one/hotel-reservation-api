import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '../Layouts/MainLayout';

export default function RoomDetail({ room }) {
    // On sécurise l'affichage au cas où roomType serait null
    const typeName = room.room_type?.name || "Standard";
    const tariffs = room.room_type?.tariffs || [];

    return (
        <MainLayout>
            <Head title={room.name} />
            <div className="py-8 max-w-6xl mx-auto">
                <Link href="/" className="text-blue-600 hover:underline mb-6 inline-block font-medium">
                    &larr; Retour à la recherche
                </Link>
                
                <div className="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
                    <div>
                        <div className="flex items-center gap-3 mb-2">
                            <h1 className="text-3xl md:text-4xl font-bold text-gray-900">{room.name}</h1>
                            <span className="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
                                {typeName}
                            </span>
                        </div>
                    </div>
                    <div className="text-2xl font-bold text-blue-600">
                        {room.price} € <span className="text-base font-normal text-gray-500">/ nuit (Prix de base)</span>
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

                {/* Informations et Réservation */}
                <div className="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-12 justify-between items-start">
                    <div className="flex-1">
                        <h2 className="text-2xl font-semibold mb-4 text-gray-800">À propos de cette chambre</h2>
                        <p className="text-gray-600 leading-relaxed text-lg mb-8">{room.description}</p>
                        
                        {/* Section Tarifs Optionnels */}
                        {tariffs.length > 0 && (
                            <div>
                                <h3 className="text-xl font-semibold mb-3 text-gray-800">Options tarifaires</h3>
                                <div className="space-y-3">
                                    {tariffs.map(tariff => (
                                        <div key={tariff.id} className="flex justify-between items-center p-3 border border-gray-100 rounded-lg bg-gray-50">
                                            <span className="font-medium text-gray-700">{tariff.name || "Option supplémentaire"}</span>
                                            <span className="text-blue-600 font-bold">+{tariff.amount || tariff.price} €</span>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                    
                    <div className="w-full md:w-80 shrink-0 bg-gray-50 p-6 rounded-xl border border-gray-100 sticky top-6">
                        <h3 className="font-bold text-lg mb-4 text-center">Prêt à réserver ?</h3>
                        <button className="w-full px-8 py-3 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition font-bold text-lg">
                            Passer à la réservation
                        </button>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}