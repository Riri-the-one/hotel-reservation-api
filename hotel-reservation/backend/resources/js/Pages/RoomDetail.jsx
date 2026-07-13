import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import MainLayout from '../Layouts/MainLayout';

export default function RoomDetail({ room }) {
    // NOUVEAU : On extrait les données correctement depuis l'objet et ses relations
    const typeName = room.room_type?.name || "Standard";
    const tariffs = room.room_type?.tariffs || [];
    
    const roomName = room.room_number ? `Chambre ${room.room_number}` : "Chambre";
    const roomDesc = room.room_type?.description || "Aucune description disponible pour cette chambre.";
    
    // Le prix se trouve probablement dans room_type s'il n'est pas dans room
   const rawPrice = room.room_type?.base_price || 0;
    const roomPrice = Number(rawPrice);

    // Configuration du formulaire Inertia
    const { data, setData, post, processing, errors } = useForm({
        room_id: room.id,
        guest_name: '',
        check_in: '',
        check_out: '',
    });

    // États pour le calcul dynamique du prix
    const [nights, setNights] = useState(0);
    const [totalPrice, setTotalPrice] = useState(0);

    // Effet pour calculer le prix à chaque changement de date
    useEffect(() => {
        if (data.check_in && data.check_out) {
            const start = new Date(data.check_in);
            const end = new Date(data.check_out);
            const diffTime = end - start;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays > 0) {
                setNights(diffDays);
                setTotalPrice(diffDays * roomPrice);
            } else {
                setNights(0);
                setTotalPrice(0);
            }
        }
    }, [data.check_in, data.check_out, roomPrice]);

    const submitReservation = (e) => {
        e.preventDefault();
        post('/reservations');
    };

    return (
        <MainLayout>
            <Head title={roomName} />
            <div className="py-8 max-w-6xl mx-auto">
                <Link href="/" className="text-blue-600 hover:underline mb-6 inline-block font-medium">
                    &larr; Retour à la recherche
                </Link>
                
                <div className="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
                    <div>
                        <div className="flex items-center gap-3 mb-2">
                            <h1 className="text-3xl md:text-4xl font-bold text-gray-900">{roomName}</h1>
                            <span className="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
                                {typeName}
                            </span>
                        </div>
                    </div>
                    <div className="text-2xl font-bold text-blue-600">
                        {roomPrice} € <span className="text-base font-normal text-gray-500">/ nuit (Prix de base)</span>
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                    <div className="col-span-1 md:col-span-2 h-96 bg-gray-200 rounded-2xl flex items-center justify-center border border-gray-100 overflow-hidden relative">
                        {room.image ? (
                            <img 
                                src={room.image.startsWith('http') ? room.image : `/storage/${room.image}`} 
                                alt={`Photo de la ${roomName}`} 
                                className="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                            />
                        ) : (
                            <span className="text-gray-400 font-medium">Aucune image disponible</span>
                        )}
                    </div>
                    
                    <div className="flex flex-col gap-4">
                        <div className="h-44 bg-gray-200 rounded-2xl flex items-center justify-center border border-gray-100 overflow-hidden">
                            <span className="text-gray-400 font-medium text-sm">Aperçu 2</span>
                        </div>
                        <div className="h-44 bg-gray-200 rounded-2xl flex items-center justify-center border border-gray-100 overflow-hidden">
                            <span className="text-gray-400 font-medium text-sm">Aperçu 3</span>
                        </div>
                    </div>
                </div>

                <div className="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-12 justify-between items-start">
                    <div className="flex-1">
                        <h2 className="text-2xl font-semibold mb-4 text-gray-800">À propos de cette chambre</h2>
                        <p className="text-gray-600 leading-relaxed text-lg mb-8">{roomDesc}</p>
                        
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
                    
                    <div className="w-full md:w-96 shrink-0 bg-gray-50 p-6 rounded-xl border border-gray-100 sticky top-6">
                        <h3 className="font-bold text-xl mb-6 text-gray-800">Réserver cette chambre</h3>
                        
                        <form onSubmit={submitReservation} className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                                <input 
                                    type="text" 
                                    className="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none"
                                    placeholder="Ex: John Doe"
                                    value={data.guest_name}
                                    onChange={(e) => setData('guest_name', e.target.value)}
                                    required
                                />
                                {errors.guest_name && <p className="text-red-500 text-sm mt-1">{errors.guest_name}</p>}
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Arrivée</label>
                                    <input 
                                        type="date" 
                                        className="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none"
                                        value={data.check_in}
                                        onChange={(e) => setData('check_in', e.target.value)}
                                        required
                                    />
                                    {errors.check_in && <p className="text-red-500 text-sm mt-1">{errors.check_in}</p>}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Départ</label>
                                    <input 
                                        type="date" 
                                        className="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none"
                                        value={data.check_out}
                                        onChange={(e) => setData('check_out', e.target.value)}
                                        required
                                    />
                                    {errors.check_out && <p className="text-red-500 text-sm mt-1">{errors.check_out}</p>}
                                </div>
                            </div>

                            {nights > 0 && (
                                <div className="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                                    <div className="flex justify-between text-sm text-blue-800 mb-2">
                                        <span>{roomPrice} € x {nights} nuits</span>
                                        <span>{totalPrice} €</span>
                                    </div>
                                    <div className="flex justify-between font-bold text-lg text-blue-900 border-t border-blue-200 pt-2">
                                        <span>Total</span>
                                        <span>{totalPrice} €</span>
                                    </div>
                                </div>
                            )}

                            <button 
                                type="submit" 
                                disabled={processing || nights <= 0}
                                className="w-full mt-6 px-8 py-3 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition font-bold text-lg disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? 'Réservation...' : 'Confirmer la réservation'}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}