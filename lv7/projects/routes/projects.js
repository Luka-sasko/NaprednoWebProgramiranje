const express = require('express');
const router = express.Router();
const mongoose = require('mongoose');
const Project = require('../models/project');

// Funkcija za provjeru valjanog ObjectId
const isValidObjectId = (id) => {
  return mongoose.Types.ObjectId.isValid(id);
};

// Projekti gdje je voditelj
router.get('/moji-projekti', ensureAuth, async (req, res) => {
  const projekti = await Project.find({ voditelj: req.user._id });
  res.render('projekti_voditelj', { projekti });
});

// Projekti gdje je član
router.get('/projekti-clan', ensureAuth, async (req, res) => {
  const projekti = await Project.find({ clanovi: req.user._id });
  res.render('projekti_clan', { projekti });
});

// Arhiva svih (član/voditelj)
router.get('/arhiva', ensureAuth, async (req, res) => {
  const projekti = await Project.find({
    arhiviran: true,
    $or: [
      { voditelj: req.user._id },
      { clanovi: req.user._id }
    ]
  });
  res.render('arhiva', { projekti });
});

// Svi projekti
router.get('/', async (req, res) => {
  const projects = await Project.find();
  res.render('index', { projects });
});

// Nova forma
router.get('/new', (req, res) => {
  res.render('new');
});

// Kreiraj projekt
router.post('/', ensureAuth, async (req, res) => {
  const { naziv, opis, cijena, obavljeni_poslovi, datum_pocetka, datum_zavrsetka, clanovi } = req.body;
  // Validacija obaveznih polja
  if (!naziv || !opis || !cijena || !obavljeni_poslovi || !datum_pocetka || !datum_zavrsetka) {
    req.flash('error_msg', 'Sva polja su obavezna!');
    return res.redirect('/projects/new');
  }
  const data = {
    naziv,
    opis,
    cijena: Number(cijena),
    obavljeni_poslovi,
    datum_pocetka: new Date(datum_pocetka),
    datum_zavrsetka: new Date(datum_zavrsetka),
    clanovi: Array.isArray(clanovi) ? clanovi : [clanovi],
    voditelj: req.user._id,
    arhiviran: false
  };
  await Project.create(data);
  req.flash('success_msg', 'Projekt uspješno kreiran!');
  res.redirect('/projects');
});

// Detalji
router.get('/:id', async (req, res) => {
  if (!isValidObjectId(req.params.id)) {
    req.flash('error_msg', 'Nevaljani ID projekta!');
    return res.redirect('/projects');
  }
  const project = await Project.findById(req.params.id);
  if (!project) {
    req.flash('error_msg', 'Projekt nije pronađen!');
    return res.redirect('/projects');
  }
  res.render('show', { project });
});

// Edit forma
router.get('/:id/edit', async (req, res) => {
  if (!isValidObjectId(req.params.id)) {
    req.flash('error_msg', 'Nevaljani ID projekta!');
    return res.redirect('/projects');
  }
  const project = await Project.findById(req.params.id);
  if (!project) {
    req.flash('error_msg', 'Projekt nije pronađen!');
    return res.redirect('/projects');
  }
  res.render('edit', { project });
});

// Ažuriraj
router.put('/:id', ensureAuth, async (req, res) => {
  if (!isValidObjectId(req.params.id)) {
    req.flash('error_msg', 'Nevaljani ID projekta!');
    return res.redirect('/projects');
  }
  const project = await Project.findById(req.params.id);
  if (!project) {
    req.flash('error_msg', 'Projekt nije pronađen!');
    return res.redirect('/projects');
  }
  
  const { naziv, opis, cijena, obavljeni_poslovi, datum_pocetka, datum_zavrsetka, clanovi, arhiviran } = req.body;
  
  // Validacija obaveznih polja
  if (!naziv || !opis || !cijena || !obavljeni_poslovi || !datum_pocetka || !datum_zavrsetka) {
    req.flash('error_msg', 'Sva polja su obavezna!');
    return res.redirect(`/projects/${req.params.id}/edit`);
  }

  // Provjera tko uređuje
  if (project.voditelj.toString() === req.user._id.toString()) {
    // Voditelj može uređivati sve
    const data = {
      naziv,
      opis,
      cijena: Number(cijena),
      obavljeni_poslovi,
      datum_pocetka: new Date(datum_pocetka),
      datum_zavrsetka: new Date(datum_zavrsetka),
      clanovi: Array.isArray(clanovi) ? clanovi : [clanovi],
      arhiviran: arhiviran === 'on'
    };
    await Project.findByIdAndUpdate(req.params.id, data);
    req.flash('success_msg', 'Projekt uspješno ažuriran!');
  } else if (project.clanovi.includes(req.user._id)) {
    // Član može uređivati samo svoje podatke
    req.flash('error_msg', 'Članovi mogu uređivati samo svoje podatke!');
    return res.redirect(`/projects/${req.params.id}`);
  } else {
    req.flash('error_msg', 'Nemate ovlasti za uređivanje ovog projekta!');
    return res.redirect(`/projects/${req.params.id}`);
  }
  res.redirect(`/projects/${req.params.id}`);
});

// Obriši
router.delete('/:id', ensureAuth, async (req, res) => {
  if (!isValidObjectId(req.params.id)) {
    req.flash('error_msg', 'Nevaljani ID projekta!');
    return res.redirect('/projects');
  }
  const project = await Project.findById(req.params.id);
  if (!project) {
    req.flash('error_msg', 'Projekt nije pronađen!');
    return res.redirect('/projects');
  }
  if (project.voditelj.toString() !== req.user._id.toString()) {
    req.flash('error_msg', 'Samo voditelj može obrisati projekt!');
    return res.redirect('/projects');
  }
  await Project.findByIdAndDelete(req.params.id);
  req.flash('success_msg', 'Projekt uspješno obrisan!');
  res.redirect('/projects');
});

function ensureAuth(req, res, next) {
  if (req.isAuthenticated()) return next();
  res.redirect('/login');
}

module.exports = router;