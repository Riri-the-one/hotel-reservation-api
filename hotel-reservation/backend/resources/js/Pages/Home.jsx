import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import MainLayout from '../Layouts/MainLayout';

// On accepte maintenant "rooms" et "initialFilters" depuis Laravel
export default function Home({ rooms, initialFilters = {} }) {
    
    // On initialise le formulaire avec les filtres existants (s'il y en a)
    const [filters, setFilters] = useState({
        arrival: initialFilters.arrival || '',
        departure: initialFilters.departure || '',
        type: initialFilters.type || '',
        maxPrice: initialFilters.maxPrice || 300,
    });

    const handleSearch = (e) => {
        e.preventDefault();
        // Le router envoie les filtres à Laravel (qui va recharger la page avec les bons résultats)
        router.get('/', filters, {
            preserveState: true,
            replace: true
        });
    };

    return (
        <MainLayout>
            <Head title="Accueil" />
            
            <div className="flex flex-col items-center text-center py-16">
                <h1 className="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Trouvez la chambre idéale
                </h1>
                <p className="text-gray-600 text-lg mb-8 max-w-2xl">
                    Gérez vos réservations, vos clients et vos disponibilités en temps réel grâce à notre plateforme.
                </p>
            </div>

            <div className="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow-lg border border-gray-100 mb-16 -mt-8 relative z-10">
                <form onSubmit={handleSearch} className="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Arrivée</label>
                        <input 
                            type="date" 
                            className="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            value={filters.arrival}
                            onChange={(e) => setFilters({...filters, arrival: e.target.value})}
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Départ</label>
                        <input 
                            type="date" 
                            className="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            value={filters.departure}
                            onChange={(e) => setFilters({...filters, departure: e.target.value})}
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select 
                            className="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white"
                            value={filters.type}
                            onChange={(e) => setFilters({...filters, type: e.target.value})}
                        >
                            <option value="">Tous les types</option>
                            <option value="standard">Standard</option>
                            <option value="suite">Suite Premium</option>
                            <option value="loft">Loft Familial</option>
                        </select>
                    </div>
                    <div>
                        <button 
                            type="submit" 
                            className="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg transition shadow-md"
                        >
                            Rechercher
                        </button>
                    </div>
                </form>

                <div className="mt-6 pt-4 border-t border-gray-100">
                    <div className="flex justify-between items-center mb-2">
                        <label className="text-sm font-medium text-gray-700">Prix maximum</label>
                        <span className="font-bold text-blue-600">{filters.maxPrice} €</span>
                    </div>
                    <input 
                        type="range" 
                        min="50" 
                        max="500" 
                        step="10"
                        className="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                        value={filters.maxPrice}
                        onChange={(e) => setFilters({...filters, maxPrice: e.target.value})}
                    />
                </div>
            </div>

            <div className="pb-12">
                <h2 className="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Nos Chambres Disponibles</h2>
                
                {rooms && rooms.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {rooms.map((room) => (
                            <div key={room.id} className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col">
                                <div className="h-48 bg-gray-200 flex items-center justify-center">
                                    <span className="text-gray-400">Image de la chambre</span>
                                </div>
                                <div className="p-5 flex flex-col flex-grow">
                                    <h3 className="text-xl font-semibold text-gray-900 mb-2">
                                        {room.name || `Chambre #${room.id}`}
                                    </h3>
                                    <p className="text-gray-500 mb-4 line-clamp-2 flex-grow">
                                        {room.description || "Une magnifique chambre équipée de tout le confort nécessaire."}
                                    </p>
                                    <div className="flex justify-between items-center pt-4 border-t border-gray-50">
                                        <span className="text-lg font-bold text-blue-600">
                                            {room.price || "---"} € <span className="text-sm font-normal text-gray-500">/ nuit</span>
                                        </span>
                                        <Link href={`/chambres/${room.id}`} className="px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-600 hover:text-white transition font-medium">
                                            Voir et Réserver
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                        <p className="text-gray-500">Aucune chambre ne correspond à vos critères de recherche.</p>
                    </div>
                )}
            </div>
        </MainLayout>
    );
}