import { Head, router } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
export default function Reservations({ reservations }) {
    return <MainLayout><Head title="Gestion des réservations" /><h1 className="mb-8 text-3xl font-semibold">Gestion des réservations</h1>
        <div className="space-y-4">{reservations.map(r => <article key={r.id} className="rounded-xl border border-stone-200 bg-white p-5">
            <h2 className="font-semibold">#{r.id} · Chambre {r.room?.room_number || 'archivée'} · {r.guest_name}</h2>
            <p className="my-3 text-sm text-stone-600">{r.check_in} → {r.check_out} · {r.total_price} € · {r.status}</p>
            <div className="flex flex-wrap gap-4">
                {r.status === 'confirmed' && <button className="rounded-lg bg-slate-900 px-4 py-2 text-sm text-white" onClick={() => router.post('/admin/reservations/' + r.id + '/check-in')}>Check-in</button>}
                {r.status === 'checked_in' && <button className="rounded-lg bg-slate-900 px-4 py-2 text-sm text-white" onClick={() => router.post('/admin/reservations/' + r.id + '/check-out')}>Check-out</button>}
            </div>
        </article>)}{!reservations.length && <p>Aucune réservation.</p>}</div>
    </MainLayout>;
}
