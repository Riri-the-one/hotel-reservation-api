import React, { useState, useEffect } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

export default function RoomDetail({ room }) {
    const { auth } = usePage().props;
    
    // Extraction des données avec sécurité (optional chaining)
    const typeName = room.room_type?.name || "Standard";
    const tariffs = room.room_type?.tariffs || [];
    const roomName = room.room_number ? `Chambre ${room.room_number}` : "Chambre";
    const roomDesc = room.room_type?.description || "Aucune description disponible pour cette chambre.";
    
    // Prix de base depuis la nouvelle colonne
    const rawPrice = room.room_type?.base_price || 0;
    const roomPrice = Number(rawPrice);

    // Configuration du formulaire Inertia
    const { data, setData, post, processing, errors } = useForm({
        room_id: room.id,
        guest_name: auth?.user?.name || '',
        check_in: '',
        check_out: '',
    });

    const [nights, setNights] = useState(0);
    const [totalPrice, setTotalPrice] = useState(0);

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
        
        // Check if user is authenticated
        if (!auth?.user) {
            window.location.href = '/login';
            return;
        }
        
        post('/reservations');
    };

    return (
        <MainLayout>
            <Head title={roomName} />
            <div className="py-8 max-w-6xl mx-auto px-4">
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
                        {roomPrice} € <span className="text-base font-normal text-gray-500">/ nuit</span>
                    </div>
                </div>

                {/* Galerie d'images dynamique */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                    <div className="col-span-1 md:col-span-2 h-96 bg-gray-200 rounded-2xl flex items-center justify-center border border-gray-100 overflow-hidden relative">
                        {room.image ? (
                            <img src={room.image.startsWith('http') ? room.image : `/storage/${room.image}`} alt={roomName} className="w-full h-full object-cover" />
                        ) : (
                            <span className="text-gray-400">Aucune image principale</span>
                        )}
                    </div>
                    
                    <div className="flex flex-col gap-4">
                        {room.images && room.images.length > 0 ? (
                            room.images.map((img) => (
                                <div key={img.id} className="h-44 bg-gray-200 rounded-2xl border border-gray-100 overflow-hidden">
                                    <img src={img.path.startsWith('http') ? img.path : `/storage/${img.path}`} alt="Aperçu" className="w-full h-full object-cover" />
                                </div>
                            ))
                        ) : (
                            <>
                                <div className="h-44 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-center text-gray-400 text-sm">Image 2 indisponible</div>
                                <div className="h-44 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-center text-gray-400 text-sm">Image 3 indisponible</div>
                            </>
                        )}
                    </div>
                </div>

                <div className="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-12 justify-between items-start">
                    <div className="flex-1">
                        <h2 className="text-2xl font-semibold mb-4 text-gray-800">À propos</h2>
                        <p className="text-gray-600 leading-relaxed text-lg mb-8">{roomDesc}</p>
                    </div>
                    
                    <div className="w-full md:w-96 shrink-0 bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <form onSubmit={submitReservation} className="space-y-4">
                            {!auth?.user && (
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Nom complet</label>
                                    <input type="text" className="w-full border rounded-lg p-2.5" value={data.guest_name} onChange={(e) => setData('guest_name', e.target.value)} required />
                                </div>
                            )}
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Arrivée</label>
                                    <input type="date" className="w-full border rounded-lg p-2.5" value={data.check_in} onChange={(e) => setData('check_in', e.target.value)} required />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Départ</label>
                                    <input type="date" className="w-full border rounded-lg p-2.5" value={data.check_out} onChange={(e) => setData('check_out', e.target.value)} required />
                                </div>
                            </div>
                            {nights > 0 && (
                                <div className="mt-4 p-4 bg-blue-50 rounded-lg text-blue-900 font-bold flex justify-between">
                                    <span>Total ({nights} nuits)</span>
                                    <span>{totalPrice} €</span>
                                </div>
                            )}
                            <button type="submit" disabled={processing || nights <= 0} className="w-full mt-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold">
                                {processing ? 'Réservation...' : 'Confirmer la réservation'}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}