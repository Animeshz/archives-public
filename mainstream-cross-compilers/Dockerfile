FROM --platform=amd64 debian:stretch-slim

LABEL maintainer="Animesh Sahu animeshsahu19@yahoo.com"

COPY scripts/linux32 scripts/linux64 scripts/windows32 scripts/windows64 /usr/local/bin/

SHELL ["/bin/bash", "-c"]

RUN \
# ==================================== Initial setup ====================================
#
build_deps=( \
    autoconf \
    automake \
    autopoint \
    bison \
    bzip2 \
    dos2unix \
    flex \
    g++-multilib \
    gettext \
    git \
    gperf \
    intltool \
    libc6-dev-i386 \
    libgdk-pixbuf2.0-dev \
    libltdl-dev \
    libssl-dev \
    libtool-bin \
    libxml-parser-perl \
    lzip \
    openssl \
    p7zip-full \
    patch \
    perl \
    python \
    ruby \
    unzip \
    wget \
    xz-utils \
) && \
#
apt-get update && \
apt-get install --no-install-recommends -y "${build_deps[@]}" bash curl sed g++ make && \
#
#
dos2unix /usr/local/bin/* && \
chmod +x /usr/local/bin/* && \
#
mkdir -p /opt && \
#
#
# ==================================== Setup Windows compilers ====================================
#
cd /opt && \
git clone https://github.com/mxe/mxe.git && \
cd mxe && \
git checkout 29bdf5b0692e1032eb1aa648f39a22f923a3d29d && \
#
echo "" >> settings.mk && \
sed -i \
    -e "$ a MXE_TARGETS := x86_64-w64-mingw32.shared i686-w64-mingw32.shared" \
    -e "$ a MXE_USE_CCACHE :=" \
    -e "$ a MXE_PLUGIN_DIRS := plugins/gcc10" \
    -e "$ a LOCAL_PKG_LIST := cc cmake" \
    -e "$ a .DEFAULT local-pkg-list:" \
    -e "$ a local-pkg-list: \$(LOCAL_PKG_LIST)" \
    -e "/^$/d" \
    settings.mk && \
#
make JOBS=$(nproc) && \
#
# remove everything except usr directory
ls | grep -v usr | xargs rm -rf && \
#
#
# # ==================================== Setup Linux compilers ====================================
echo 'deb http://deb.debian.org/debian testing main' >> /etc/apt/sources.list && \
apt-get update && \
apt-get install --no-install-recommends -y gcc g++ && \
sed -i '$d' /etc/apt/sources.list && \
#
# Install cmake manually to resolve conflict with dependencies of curl
mkdir -p /opt/cmake && \
cd /opt/cmake && \
curl -LO 'https://github.com/Kitware/CMake/releases/download/v3.20.2/cmake-3.20.2-linux-x86_64.sh' && \
chmod +x cmake-3.20.2-linux-x86_64.sh && \
./cmake-3.20.2-linux-x86_64.sh --skip-license && \
rm -rf doc man cmake-3.20.2-linux-x86_64.sh && \
ln -s /opt/cmake/bin/cmake /usr/local/bin/cmake && \
#
#
# ==================================== Cleanup ====================================
#
apt-get autoremove --purge -y "${build_deps[@]}" && \
apt-get clean -y

ENV WORK_DIR /work
WORKDIR ${WORK_DIR}