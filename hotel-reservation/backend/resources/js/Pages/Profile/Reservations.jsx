import { Head, Link, useForm } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

const labels = { confirmed: 'Confirmée', checked_in: 'En cours', checked_out: 'Terminée', cancelled: 'Annulée' };
export default function Reservations({ reservations }) {
    const { post, processing } = useForm();
    return <MainLayout><Head title="Mes réservations" /><h1 className="mb-8 text-3xl font-semibold">Mes réservations</h1>
        {reservations.length ? <div className="space-y-5">{reservations.map(r => <article key={r.id} className="rounded-2xl border border-stone-200 bg-white p-5 sm:p-7">
            <div className="flex flex-wrap justify-between gap-4"><h2 className="text-xl font-semibold">{r.room?.room_type?.name || 'Chambre archivée'} · #{r.id}</h2><span className="rounded-full bg-stone-100 px-3 py-1 text-sm">{labels[r.status] || r.status}</span></div>
            <dl className="my-6 grid grid-cols-2 gap-4 sm:grid-cols-4"><div><dt className="text-sm text-stone-500">Arrivée</dt><dd>{r.check_in}</dd></div><div><dt className="text-sm text-stone-500">Départ</dt><dd>{r.check_out}</dd></div><div><dt className="text-sm text-stone-500">Total</dt><dd>{r.total_price} €</dd></div><div><dt className="text-sm text-stone-500">Voyageurs</dt><dd>{r.guests}</dd></div></dl>
            <div className="flex flex-wrap gap-4">{r.room && <Link className="text-sm underline" href={'/chambres/' + r.room.id}>Voir la chambre</Link>}
                {r.status === 'confirmed' && r.check_in > new Date().toLocaleDateString('en-CA') && <button disabled={processing} onClick={() => { if (confirm('Annuler cette réservation ?')) post('/reservations/' + r.id + '/cancel'); }} className="text-sm text-red-700 underline disabled:opacity-50">Annuler</button>}
            </div>
        </article>)}</div> : <div className="rounded-2xl border border-dashed border-stone-300 p-10 text-center"><p>Aucune réservation pour le moment.</p><Link href="/" className="mt-4 inline-block underline">Explorer les chambres</Link></div>}
    </MainLayout>;
}
