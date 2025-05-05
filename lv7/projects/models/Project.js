const mongoose = require('mongoose');
const Schema = mongoose.Schema;

const ProjectSchema = new Schema({
  naziv: { type: String, required: true },
  opis: { type: String, required: true },
  cijena: { type: Number, required: true },
  obavljeni_poslovi: { type: String, required: true },
  datum_pocetka: { type: Date, required: true },
  datum_zavrsetka: { type: Date, required: true },
  clanovi: [{ type: String }], // ID-ovi korisnika
  voditelj: { type: Schema.Types.ObjectId, ref: 'User', required: true },
  arhiviran: { type: Boolean, default: false }
});

module.exports = mongoose.model('Project', ProjectSchema);